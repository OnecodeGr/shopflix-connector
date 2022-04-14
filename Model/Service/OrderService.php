<?php
/**
 * OrderService.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Service;

use Exception;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderManagementInterface;
use Onecode\ShopFlixConnector\Api\Data;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Api\StatusHistoryRepositoryInterface;
use Onecode\ShopFlixConnector\Helper\Data as ConfigHelper;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Convert\Order as OrderConverter;
use Psr\Log\LoggerInterface;

class OrderService implements ManagementInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var StatusHistoryRepositoryInterface
     */
    protected $historyRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;


    /**
     * @var ManagerInterface
     */
    protected $eventManager;


    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var OrderConverter
     */
    private $converter;
    /**
     * @var ConfigHelper
     */
    private $configHelper;
    /**
     * @var OrderManagementInterface
     */
    private $salesOrderManagement;
    /**
     * @var Connector
     */
    private $connector;


    public function __construct(
        OrderRepositoryInterface         $orderRepository,
        StatusHistoryRepositoryInterface $historyRepository,
        SearchCriteriaBuilder            $criteriaBuilder,
        FilterBuilder                    $filterBuilder,
        ManagerInterface                 $eventManager,
        OrderConverter                   $converter,
        ConfigHelper                     $configHelper,
        LoggerInterface                  $logger,
        OrderManagementInterface         $salesOrderManagement
    )
    {

        $this->orderRepository = $orderRepository;
        $this->historyRepository = $historyRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->eventManager = $eventManager;
        $this->converter = $converter;
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->salesOrderManagement = $salesOrderManagement;
        $this->connector = new Connector(
            $this->configHelper->getUsername(),
            $this->configHelper->getApikey(),
            $this->configHelper->getApiUrl(),
            $this->configHelper->getTimeModifier()
        );
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function accept(int $id, bool $synced = false): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canAccept()) {
            $order->accept($synced);

            if ($this->configHelper->toOrder()) {
                $this->converter->toMagentoOrder($order);
            }
            try {
                $this->connector->picking($order->getShopFlixOrderId());
            } catch (Exception $e) {
                $order->setSynced(false);
            }

            $this->orderRepository->save($order);
            return true;
        }

        return false;
    }


    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function reject(int $id, string $message = '', $synced = false): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canReject()) {
            $order->reject($message, $synced);
            try{
                $this->connector->rejected($order->getShopFlixOrderId(), $message);
            }catch (\Exception  $e){
                $order->setSynced(false);
            }

            $this->orderRepository->save($order);
            return true;
        }

        return false;
    }

    public function getCommentsList(int $id): Data\StatusHistorySearchResultInterface
    {
        $this->criteriaBuilder->addFilters([$this->filterBuilder->setField('parent_id')->setValue($id)->setConditionType('eq')->create()]);
        $searchCriteria = $this->criteriaBuilder->create();
        return $this->historyRepository->getList($searchCriteria);
    }

    public function addComment(int $id, Data\StatusHistoryInterface $statusHistory): bool
    {
        $order = $this->orderRepository->getById($id);
        $order->addStatusHistory($statusHistory);
        $this->orderRepository->save($order);

        return true;
    }

    public function getStatus(int $id): string
    {
        return $this->orderRepository->getById($id)->getStatus();
    }

    public function readyToBeShipped(int $id, bool $synced = false): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canReadyToBeShipped()) {
            try {
                $order->readyToBeShipped('', $synced);
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
            return true;
        }

        return false;
    }


    public function cancel(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canCancel()) {
            try {
                $order->cancel();
                if ($this->configHelper->toOrder() && $order->getMagentoOrderId()) {
                    $this->salesOrderManagement->cancel($order->getMagentoOrderId());
                }
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
            return true;
        }
        return false;
    }

    public function partialShipped(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canPartialShipped()) {
            try {
                $order->partialShipped();

            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
            return true;
        }
        return false;
    }

    public function shipped(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canShipped()) {
            try {
                $order->shipped();
                if ($this->configHelper->toOrder() && $order->getMagentoOrderId()) {
                    if ($order->getMagentoRealOrder()->canInvoice()) {
                        $this->converter->invoice($order->getMagentoRealOrder());
                    }
                    if ($order->getMagentoRealOrder()->canShip()) {
                        $this->converter->ship($order->getMagentoRealOrder());
                    }

                }
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
            return true;
        }
        return false;
    }

    public function onTheWay(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canOnTheWay()) {
            try {
                $order->onTheWay();
                if ($this->configHelper->toOrder() && $order->getMagentoOrderId()) {
                    if ($order->getMagentoRealOrder()->canInvoice()) {
                        $this->converter->invoice($order->getMagentoRealOrder());
                    }
                    if ($order->getMagentoRealOrder()->canShip()) {
                        $this->converter->ship($order->getMagentoRealOrder());
                    }

                }
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
            return true;
        }
        return false;
    }


    public function completed(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canCompleted()) {
            try {
                $order->complete();
                if ($this->configHelper->toOrder() && $order->getMagentoOrderId()) {
                    if ($order->getMagentoRealOrder()->canInvoice()) {
                        $this->converter->invoice($order->getMagentoRealOrder());
                    }
                    if ($order->getMagentoRealOrder()->canShip()) {
                        $this->converter->ship($order->getMagentoRealOrder());
                    }

                }
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
            return true;
        }
        return false;
    }
}
