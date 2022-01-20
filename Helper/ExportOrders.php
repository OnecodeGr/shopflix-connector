<?php
/**
 * ExportOrders.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Onecode\ShopFlixConnector\Api\AddressRepositoryInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Api\ItemRepositoryInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\AddressFactory;
use Onecode\ShopFlixConnector\Model\Order\ItemFactory;
use Onecode\ShopFlixConnector\Model\OrderFactory;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class ExportOrders
{
    private $_helper;
    private $_connector;
    private $_orderFactory;
    private $_orderRepository;
    private $_logger;
    private $_productRepository;
    private $_itemFactory;
    private $_addressFactory;
    private $_itemRepository;
    private $_orderAddressRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Data $data
     * @param Connector $connector
     * @param OrderFactory $orderFactory
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     * @param ProductRepository $productRepository
     * @param ItemFactory $itemFactory
     * @param AddressFactory $addressFactory
     * @param ItemRepositoryInterface $itemRepository
     * @param AddressRepositoryInterface $orderAddressRepository
     */
    public function __construct(Data                       $data,
                                OrderFactory               $orderFactory,
                                OrderRepositoryInterface   $orderRepository,
                                LoggerInterface            $logger,
                                ProductRepository          $productRepository,
                                ItemFactory                $itemFactory,
                                AddressFactory             $addressFactory,
                                ItemRepositoryInterface    $itemRepository,
                                AddressRepositoryInterface $orderAddressRepository,
                                SearchCriteriaBuilder      $searchCriteriaBuilder


    )
    {
        $this->_helper = $data;

        $this->_orderFactory = $orderFactory;
        $this->_orderRepository = $orderRepository;
        $this->_productRepository = $productRepository;
        $this->_itemFactory = $itemFactory;
        $this->_addressFactory = $addressFactory;
        $this->_itemRepository = $itemRepository;
        $this->_orderAddressRepository = $orderAddressRepository;
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
