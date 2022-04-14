<?php
/**
 * ExportReturnOrders.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ReturnOrder;
use Psr\Log\LoggerInterface;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Status\History;
use Onecode\ShopFlixConnector\Library\Connector;

class ExportReturnOrders
{
    /**
     * @var Data
     */
    private $_helper;
    /**
     * @var ReturnOrderRepositoryInterface
     */
    private $_orderRepository;
    /**
     * @var LoggerInterface
     */
    private $_logger;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Connector
     */
    private $_connector;

    public function __construct(
        Data                           $data,
        ReturnOrderRepositoryInterface $orderRepository,
        LoggerInterface                $logger,
        SearchCriteriaBuilder          $searchCriteriaBuilder


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
        $this->searchCriteriaBuilder->addFilter(ReturnOrderInterface::SYNC, false);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $orders = $this->_orderRepository->getList($searchCriteria);
        /** @var ReturnOrderInterface $order */
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
     * @param ReturnOrderInterface|ReturnOrder $order
     * @throws Exception
     */
    private function processOrder($order)
    {
        switch ($order->getStatus()) {
            case ReturnOrderStatusInterface::STATUS_RETURN_APPROVED:
                $this->_connector->approveReturnedOrder($order->getShopFlixOrderId());
                break;
            case ReturnOrderStatusInterface::STATUS_RETURN_DECLINED:
                $historyCollection = $order->getStatusHistoryForShopFlix();
                $message = [];
                /** @var History $history */
                foreach ($historyCollection as $history) {
                    $message[] = $history->getComment();
                }
                $this->_connector->declineReturnedOrder($order->getShopFlixOrderId(), implode(PHP_EOL, $message));
                break;
        }

    }
}
