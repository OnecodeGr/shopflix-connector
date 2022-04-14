<?php
/**
 * ImportReturnOrders.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderAddressInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderManagementInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderRepositoryInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\ReturnOrder;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Item;
use Psr\Log\LoggerInterface;

class ImportReturnOrders
{

    /**
     * @var Data
     */
    private $_helper;
    /**
     * @var ReturnOrderInterfaceFactory
     */
    private $_orderFactory;
    /**
     * @var ProductRepository
     */
    private $_productRepository;
    /**
     * @var ReturnOrderItemInterfaceFactory
     */
    private $_itemFactory;
    /**
     * @var ReturnOrderAddressInterfaceFactory
     */
    private $_addressFactory;
    /**
     * @var LoggerInterface
     */
    private $_logger;
    /**
     * @var Connector
     */
    private $_connector;
    /**
     * @var ReturnOrderRepositoryInterface
     */
    private $_orderRepository;
    /**
     * @var OrderRepositoryInterface
     */
    private $_parentOrderRepository;
    /**
     * @var ReturnOrderManagementInterface
     */
    private $_orderManagement;

    public function __construct(
        Data                               $data,
        OrderRepositoryInterface           $parentOrderRepository,
        ReturnOrderRepositoryInterface     $orderRepository,
        ReturnOrderInterfaceFactory        $orderFactory,
        ProductRepository                  $productRepository,
        ReturnOrderItemInterfaceFactory    $itemFactory,
        ReturnOrderAddressInterfaceFactory $addressFactory,
        ReturnOrderManagementInterface     $orderManagement,
        LoggerInterface                    $logger
    )
    {
        $this->_helper = $data;
        $this->_orderFactory = $orderFactory;
        $this->_parentOrderRepository = $parentOrderRepository;
        $this->_orderRepository = $orderRepository;
        $this->_productRepository = $productRepository;
        $this->_itemFactory = $itemFactory;
        $this->_addressFactory = $addressFactory;
        $this->_orderManagement = $orderManagement;
        $this->_logger = $logger;

    }

    public function import()
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

        $newOrders = $this->_connector->getNewReturnedOrders();
        foreach ($newOrders as $order) {
            $this->processOrder($order);
        }

        $onTheWayToStoreOrders = $this->_connector->getOnTheWayToStoreReturnedOrders();
        foreach ($onTheWayToStoreOrders as $order) {
            $this->processOnTheWayToStoreOrder($order);
        }

        $deliveredOrders = $this->_connector->getDeliveredToStoreReturnedOrders();

        foreach ($deliveredOrders as $order) {
            $this->processDeliveredToStoreOrder($order);
        }

        $approvedOrders = $this->_connector->getApprovedReturnOrders();
        foreach ($approvedOrders as $order) {
            $this->processApprovedOrder($order);
        }

        $declinedOrders = $this->_connector->getDeclinedReturnedOrders();
        foreach ($declinedOrders as $order) {
            $this->processDeclineOrder($order);
        }
        $completedOrders = $this->_connector->getCompletedReturnedOrders();
        foreach ($completedOrders as $order) {
            $this->processCompleteOrder($order);
        }
    }

    /**
     * @param $data
     * @param string $status
     * @param string $state
     * @return ReturnOrderInterface|ReturnOrder|void
     */
    private function processOrder(
        $data,
        string $status = ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED,
        string $state = ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX
    )
    {
        $items = [];
        $price = 0;
        foreach ($data['items'] as $item) {
            try {
                $product = $this->_productRepository->get($item['sku']);
                $item['name'] = $product->getName();
                $item['product_id'] = $product->getId();
                $item['product_type'] = $product->getTypeId();
                $price += $item['price'] * $item['qty'];
                $items[] = $this->_itemFactory->create()->setData($item);
            } catch (NoSuchEntityException $e) {
            }
        }

        if (empty($items)) {
            return;
        }

        try {
            $order = $this->_orderRepository->getByIncrementId($data['return_order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->_orderFactory->create();
        }


        try {
            $parentOrder = $this->_parentOrderRepository->getByIncrementId($data['return_order']['shopflix_parent_order_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            return;
        }

        if ($order->getState() != ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX && !$order->isObjectNew()) {
            $this->_logger->info(__("SHOPFLIX Return Order %1 has %2", $data['order']['increment_id'], $order->getStatusLabel()));
            return;
        }

        foreach ($items as $item) {
            /** @var Item $orderedItem */
            foreach ($order->getItems() as $orderedItem) {
                if ($item->getSku() === $orderedItem->getSku()) {
                    $item->setId($orderedItem->getId());
                }
            }
        }

        $order->addData($data['return_order']);
        $order->setState($state);
        $order->setStatus($status);
        $order->setParentId($parentOrder->getId());
        $order->setTotalPaid($price);
        $order->setSubtotal($price);
        foreach ($data['addresses'] as $address) {
            $address = $this->_addressFactory->create()->setData($address);
            switch ($address->getAddressType()) {
                case "billing":
                    $order->setBillingAddress($address);
                    break;
                case "shipping":
                    $order->setShippingAddress($address);
                    break;
            }
        }

        $order->setItems($items);
        $this->_orderRepository->save($order);

        return $order;
    }

    private function processOnTheWayToStoreOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['return_order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processOrder(
                $data,
                ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED,
                ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX
            );
        }

        if ($order && $order->canOnTheWay()) {
            $this->_orderManagement->onTheWay($order->getEntityId());
        }
    }

    private function processDeliveredToStoreOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['return_order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processOrder(
                $data,
                ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED,
                ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX
            );
        }
        if ($order && $order->canDelivered()) {
            $this->_orderManagement->delivered($order->getEntityId());
        }
    }

    private function processApprovedOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['return_order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processOrder(
                $data,
                ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE,
                ReturnOrderInterface::STATE_DELIVERED_TO_THE_STORE
            );
        }
        if ($order && $order->canApprove()) {
            $this->_orderManagement->approved($order->getEntityId(), false);
        }
    }

    private function processDeclineOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['return_order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processOrder(
                $data,
                ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE,
                ReturnOrderInterface::STATE_DELIVERED_TO_THE_STORE
            );
        }
        if ($order && $order->canDecline()) {
            $this->_orderManagement->declined($order->getEntityId(), '', false);
        }
    }
}
