<?php
/**
 * ImportOrders.php
 *
 * @copyright Copyright © 2021 Onecode All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;


use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\AddressRepositoryInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order\AddressFactory;
use Onecode\ShopFlixConnector\Model\Order\Item;
use Onecode\ShopFlixConnector\Model\Order\ItemFactory;
use Onecode\ShopFlixConnector\Model\OrderFactory;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class ImportOrders
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
    public function __construct(Data                       $data,
                                OrderFactory               $orderFactory,
                                OrderRepositoryInterface   $orderRepository,
                                LoggerInterface            $logger,
                                ProductRepository          $productRepository,
                                ItemFactory                $itemFactory,
                                AddressFactory             $addressFactory,
                                ManagementInterface        $orderManagement
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
     * @throws Exception
     */
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
        $newOrders = $this->_connector->getNewOrders();
        foreach ($newOrders as $order) {
            $this->processNewOrder($order);
        }


        $canceledOrders = $this->_connector->getCancelOrders();
        foreach ($canceledOrders as $canceledOrder) {
            $this->processCancelOrder($canceledOrder);
        }

        $onTheWayOrders = $this->_connector->getOnTheWayOrders();
        foreach ($onTheWayOrders as $onTheWayOrder) {
            $this->processOnTheWayOrder($onTheWayOrder);
        }

        $partialShippedOrders = $this->_connector->getPartialShipped();
        foreach ($partialShippedOrders as $partialShippedOrder) {
            $this->processPartialShippedOrder($partialShippedOrder);
        }

        $shippedOrders = $this->_connector->getShipped();
        foreach ($shippedOrders as $shippedOrder) {
            $this->processShippedOrder($shippedOrder);
        }

        $completedOrders = $this->_connector->getCompletedOrders();
        foreach ($completedOrders as $completedOrder) {
            $this->processCompletedOrder($completedOrder);
        }
    }

    /**
     * @param $data
     * @return OrderInterface|\Onecode\ShopFlixConnector\Model\Order|void
     */
    private function processNewOrder($data)
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
            $order = $this->_orderRepository->getByIncrementId($data['order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->_orderFactory->create();
        }


        if ($order->getState() != OrderInterface::STATE_PENDING_ACCEPTANCE && !$order->isObjectNew()) {
            $this->_logger->info(__("SHOPFLIX Order %1 has %2", $data['order']['increment_id'], $order->getStatusLabel()));
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


        $order->addData($data['order']);
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

        if ($this->_helper->autoAccept()) {
            $this->autoAcceptOrder($order);
        }
        return $order;
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    private function autoAcceptOrder(OrderInterface $order)
    {
        if ($order->canAutoAccept()) {
            try {
                $this->_orderManagement->accept($order->getEntityId());
            } catch (Exception $e) {
                $this->_logger->critical($e);
            }
        }
    }

    /**
     * @param $data
     * @return void
     */
    private function processCancelOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processNewOrder($data);
        }

        if ($order->canCancel()) {
            $this->_orderManagement->cancel($order->getEntityId());
        }

    }

    /**
     * @param $data
     * @return void
     */
    private function processOnTheWayOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processNewOrder($data);
        }

        if ($order->canOnTheWay()) {
            $this->_orderManagement->onTheWay($order->getEntityId());
        }

    }

    /**
     * @param $data
     * @return void
     */
    private function processPartialShippedOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processNewOrder($data);
        }

        if ($order->canPartialShipped()) {
            $this->_orderManagement->partialShipped($order->getEntityId());
        }

    }

    /**
     * @param $data
     * @return void
     */
    private function processShippedOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processNewOrder($data);
        }

        if ($order->canShipped()) {
            $this->_orderManagement->shipped($order->getEntityId());
        }

    }

    /**
     * @param $data
     * @return void
     */
    private function processCompletedOrder($data)
    {
        try {
            $order = $this->_orderRepository->getByIncrementId($data['order']['increment_id']);
        } catch (NoSuchEntityException $e) {
            $this->_logger->info($e);
            $order = $this->processNewOrder($data);
        }

        if ($order->canCompleted()) {
            $this->_orderManagement->completed($order->getEntityId());
        }

    }
}
