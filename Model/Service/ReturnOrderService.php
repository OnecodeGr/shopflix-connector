<?php
/**
 * ReturnOrderService.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Service;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data;
use Onecode\ShopFlixConnector\Api\ReturnOrderManagementInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderRepositoryInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderStatusHistoryRepositoryInterface;
use Onecode\ShopFlixConnector\Helper\Data as ConfigHelper;
use Onecode\ShopFlixConnector\Library\Connector;
use Psr\Log\LoggerInterface;

class ReturnOrderService implements ReturnOrderManagementInterface
{

    /**
     * @var ReturnOrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;
    /**
     * @var ReturnOrderStatusHistoryRepositoryInterface
     */
    private $historyRepository;
    /**
     * @var ConfigHelper
     */
    private $configHelper;
    /**
     * @var Connector
     */
    private $connector;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        ReturnOrderRepositoryInterface              $orderRepository,
        SearchCriteriaBuilder                       $criteriaBuilder,
        ReturnOrderStatusHistoryRepositoryInterface $historyRepository,
        ConfigHelper                                $configHelper,
        LoggerInterface                             $logger,
        FilterBuilder                               $filterBuilder,
        ManagerInterface                            $eventManager
    )
    {
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->historyRepository = $historyRepository;
        $this->configHelper = $configHelper;
        $this->logger = $logger;
        $this->filterBuilder = $filterBuilder;
        $this->eventManager = $eventManager;
        $this->connector = new Connector(
            $this->configHelper->getUsername(),
            $this->configHelper->getApikey(),
            $this->configHelper->getApiUrl(),
            $this->configHelper->getTimeModifier()
        );
    }


    /**
     * @throws NoSuchEntityException
     */
    public function approved(int $id , $sync = true): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canApprove()) {
            try {
                $order->approved();
                if($sync){
                    try {
                        $this->connector->approveReturnedOrder($order->getShopFlixOrderId());
                    } catch (\Exception $e) {
                        $order->setSynced(false);
                    }
                }
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
        }
        return true;
    }

    public function declined(int $id, string $message = '', bool $sync = true): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canDecline()) {
            try {
                $order->decline();
                if($sync){
                    try {
                        $this->connector->declineReturnedOrder($order->getShopFlixOrderId());
                    } catch (\Exception $e) {
                        $order->setSynced(false);
                    }
                }
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
        }
        return true;
    }

    public function getCommentsList(int $id): Data\ReturnOrderStatusHistorySearchResultInterface
    {
        $this->criteriaBuilder->addFilters([$this->filterBuilder->setField('parent_id')->setValue($id)->setConditionType('eq')->create()]);
        $searchCriteria = $this->criteriaBuilder->create();
        return $this->historyRepository->getList($searchCriteria);
    }

    public function addComment(int $id, Data\ReturnOrderStatusHistoryInterface $statusHistory): bool
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

    public function completed(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canComplete()) {
            try {
                $order->complete();
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
        }
        return true;
    }

    public function onTheWay(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canOnTheWay()) {
            try {
                $order->onTheWay();
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);

        }
        return true;
    }

    public function delivered(int $id): bool
    {
        $order = $this->orderRepository->getById($id);
        if ($order->canDelivered()) {
            try {
                $order->delivered();
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
                return false;
            }
            $this->orderRepository->save($order);
        }
        return true;
    }
}
