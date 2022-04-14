<?php
/**
 * ExportOrders.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class ExportOrders
{
    private $_helper;
    private $_connector;
    private $_orderRepository;
    private $_logger;
    private $searchCriteriaBuilder;

    /**
     * @param Data $data
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Data                     $data,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface          $logger,
        SearchCriteriaBuilder    $searchCriteriaBuilder


    )
    {
        $this->_helper = $data;
        $this->_orderRepository = $orderRepository;
        $this->_logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }


    public function export()
    {

        if (!$this->_helper->isEnabled()) {
            return;
        }
        $this->_connector = new Connector(
            $this->_helper->getUsername(),
            $this->_helper->getApikey(),
            $this->_helper->getApiUrl()
        );
        $this->searchCriteriaBuilder->addFilter(OrderInterface::SYNCED, false);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $orders = $this->_orderRepository->getList($searchCriteria);

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            try {
                $this->processOrder($order);
                $order->setSynced(true);
                $this->_orderRepository->save($order);
            } catch (Exception $e) {
                $this->_logger->debug($e);
            }

        }

    }

    /**
     * @param OrderInterface|Order $order
     * @throws Exception
     */
    private function processOrder($order)
    {


        switch ($order->getStatus()) {
            case StatusInterface::STATUS_PICKING:
                $this->_connector->picking($order->getShopFlixOrderId());
                break;
            case StatusInterface::STATUS_REJECTED:
                $historyCollection = $order->getStatusHistoryForShopFlix();
                $message = [];
                /** @var Order\Status\History $history */
                foreach ($historyCollection as $history) {
                    $message[] = $history->getComment();
                }
                $this->_connector->rejected($order->getShopFlixOrderId(), implode(PHP_EOL, $message));
                break;
            case StatusInterface::STATUS_READY_TO_BE_SHIPPED:
                $this->_connector->readyToBeShipped($order->getShopFlixOrderId());
                break;
        }

    }

}
