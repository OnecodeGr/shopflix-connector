<?php
/**
 * ImportShipments.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\Shipment\Item;
use Onecode\ShopFlixConnector\Model\Order\Shipment\ItemFactory;
use Onecode\ShopFlixConnector\Model\Order\Shipment\TrackFactory;
use Onecode\ShopFlixConnector\Model\Order\ShipmentFactory;
use Onecode\ShopFlixConnector\Model\Order\ShipmentRepository;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class ImportShipments
{

    private $orderRepository;
    private $searchCriteriaBuilder;
    private $connector;
    private $shipmentRepository;
    private $shipmentFactory;
    private $productRepository;
    private $itemFactory;
    private $trackFactory;
    private $logger;
    private $_helper;

    public function __construct(OrderRepository            $orderRepository,
                                SearchCriteriaBuilder      $searchCriteriaBuilder,
                                Data                       $data,
                                ShipmentRepository         $shipmentRepository,
                                ShipmentFactory            $shipmentFactory,
                                ItemFactory                $itemFactory,
                                TrackFactory               $trackFactory,
                                ProductRepositoryInterface $productRepository,
                                LoggerInterface            $logger
    )
    {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_helper = $data;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->itemFactory = $itemFactory;
        $this->trackFactory = $trackFactory;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }


    public function import()
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }
        $this->connector = new Connector(
            $this->_helper->getUsername(),
            $this->_helper->getApikey(),
            $this->_helper->getApiUrl()
        );
        $this->searchCriteriaBuilder->addFilter(
            OrderInterface::STATE,
            OrderInterface::STATE_ACCEPTED,
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $orders = $this->orderRepository->getList($searchCriteria);



        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            $shipmenData = $this->connector->getShipment($order->getShopFlixOrderId());
            foreach ($shipmenData as $shipment) {
                $this->processShipment($shipment, $order);
            }

        }
    }


    /**
     * @throws CouldNotSaveException
     * @throws InputException
     */
    private function processShipment($shipment, Order $order)
    {


        try {
            $shipmentObject = $this->shipmentRepository->getByIncrementId($shipment['shipment'][ShipmentInterface::INCREMENT_ID]);
        } catch (NoSuchEntityException $e) {
            $shipmentObject = $this->shipmentFactory->create();
        }

        if ($shipmentObject->getShipmentStatus() != 3) {


            $items = [];

            foreach ($shipment['items'] as $item) {
                $exist = false;
                try {
                    $this->productRepository->get($item['sku']);
                    foreach ($order->getItems() as $orderItem) {
                        if ($item['sku'] == $orderItem->getSku() && !$exist) {
                            $item['price'] = $orderItem->getPrice();
                            $item['name'] = $orderItem->getName();
                            $item['order_item_id'] = $orderItem->getId();
                            $item['row_total'] = $orderItem->getPrice() * $item['qty'];
                            $exist = true;
                        } else if ($exist) {
                            break;
                        }
                    }
                    $items[] = $this->itemFactory->create()->setData($item);
                } catch (NoSuchEntityException $e) {

                }

            }


            $shipment['tracks'][ShipmentTrackInterface::ORDER_ID] = $order->getId();
            $track = $this->trackFactory->create()->setData($shipment['tracks']);

            $shipment['shipment'][ShipmentInterface::BILLING_ADDRESS_ID] = $order->getBillingAddressId();
            $shipment['shipment'][ShipmentInterface::SHIPPING_ADDRESS_ID] = $order->getShippingAddressId();
            $shipment['shipment'][ShipmentInterface::ORDER_ID] = $order->getId();

            $shipmentObject->addData($shipment['shipment']);

            foreach ($items as $item) {
                /** @var Item $shipmentItem */
                foreach ($shipmentObject->getItems() as $shipmentItem) {
                    if ($item->getSku() === $shipmentItem->getSku()) {
                        $item->setId($shipmentItem->getId());
                    }
                }
            }


            /** @var Order\Shipment\Track $shipmentTrack */
            if ($shipmentObject->getTracks()) {
                foreach ($shipmentObject->getTracks() as $shipmentTrack) {
                    if ($track->getTrackNumber() === $shipmentTrack->getTrackNumber()) {
                        $track->setId($shipmentTrack->getId());
                    }
                }
            }


            $shipmentObject->setItems($items);
            if ($track->getTrackNumber()) {

                $shipmentObject->setTracks([$track]);
            }

            try {
                $this->shipmentRepository->save($shipmentObject);
            } catch (LocalizedException $e) {
                if ($shipment['shipment']['increment_id'] == 45) {
                    dd($e->getPrevious()->getMessage(), $e->getPrevious(), $e->getTraceAsString());

                }

                $this->logger->info(__("SHOPFLIX Order %1 could not save %2 shipment: {$e->getMessage()}", $order->getIncrementId(), $shipment['shipment']['increment_id']));
            }


        }
    }
}
