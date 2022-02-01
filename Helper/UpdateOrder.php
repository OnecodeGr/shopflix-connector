<?php
/**
 * UpdateOrder.php
 *
 * @copyright Copyright © 2022 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Magento\Catalog\Model\ProductRepository;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\AddressFactory;
use Onecode\ShopFlixConnector\Model\Order\ItemFactory;
use Onecode\ShopFlixConnector\Model\OrderFactory;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class UpdateOrder
{

    private $_helper;
    private $_connector;
    private $_orderFactory;
    private $_orderRepository;
    private $_logger;
    private $_productRepository;
    private $_itemFactory;
    private $_addressFactory;
    private $_orderManagement;


    /**
     * @param Data $data
     * @param OrderFactory $orderFactory
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     * @param ProductRepository $productRepository
     * @param ItemFactory $itemFactory
     * @param AddressFactory $addressFactory
     * @param ManagementInterface $orderManagement
     */
    public function __construct(Data                     $data,
                                OrderFactory             $orderFactory,
                                OrderRepositoryInterface $orderRepository,
                                LoggerInterface          $logger,
                                ProductRepository        $productRepository,
                                ItemFactory              $itemFactory,
                                AddressFactory           $addressFactory,
                                ManagementInterface      $orderManagement
    )
    {
        $this->_helper = $data;
        $this->_orderFactory = $orderFactory;
        $this->_orderRepository = $orderRepository;
        $this->_productRepository = $productRepository;
        $this->_itemFactory = $itemFactory;
        $this->_addressFactory = $addressFactory;
        $this->_logger = $logger;
        $this->_orderManagement = $orderManagement;
    }


    /**
     * @param Order $order
     * @return void
     */
    public function update($order)
    {

        if (!$this->_helper->isEnabled()) {
            return;
        }
        $this->_connector = new Connector(
            $this->_helper->getUsername(),
            $this->_helper->getApikey(),
            $this->_helper->getApiUrl(),
            $this->_helper->getTimeModifier()
        );

        $shopFlixData = $this->_connector->getOrderDetail($order->getIncrementId());

        $status = $shopFlixData['order']['status'];
        $state = $shopFlixData['order']['state'];

        if ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_READY_TO_BE_SHIPPED
            && $order->canReadyToBeShipped()) {
            $this->_orderManagement->readyToBeShipped($order->getId(), true);
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_ON_THE_WAY && $order->canOnTheWay()) {
            $this->_orderManagement->onTheWay($order->getId());
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_PARTIAL_SHIPPED && $order->canPartialShipped()) {
            $this->_orderManagement->partialShipped($order->getId());
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_SHIPPED && $order->canShipped()) {
            $this->_orderManagement->shipped($order->getId());
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_COMPLETED && $order->canCompleted()) {
            $this->_orderManagement->completed($order->getId());
        } elseif ($state == OrderInterface::STATE_CANCELED &&
            $status == StatusInterface::STATUS_CANCELED && $order->canCancel()) {
            $this->_orderManagement->cancel($order->getId());
        } elseif ($state == OrderInterface::STATE_ACCEPTED &&
            ($status == StatusInterface::STATUS_ACCEPTED || $status == StatusInterface::STATUS_PICKING)
            && $order->canAccept()) {
            $this->_orderManagement->accept($order->getId(), true);
        } elseif ($state == OrderInterface::STATE_REJECTED &&
            $status == StatusInterface::STATUS_REJECTED
            && $order->canReject()) {
            $this->_orderManagement->reject($order->getId(), '', true);
        }

    }


}
