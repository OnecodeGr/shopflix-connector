<?php
/**
 * Shipment.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Model\EntityInterface;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\Shipment\Track;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment as ResourceModel;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Item\CollectionFactory as ShipmentItemCollectionFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Track\Collection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Track\CollectionFactory as TrackCollectionFactory;

class Shipment extends AbstractModel implements EntityInterface, ShipmentInterface
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_shipment';
    /**
     * @var string
     */
    protected $_eventObject = 'shopflix_shipment';

    /** @var Order */
    private $_order;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var ShipmentItemCollectionFactory
     */
    private $shipmentItemCollectionFactory;
    /**
     * @var Collection
     */
    private $tracksCollection;
    /**
     * @var TrackCollectionFactory
     */
    private $_trackCollectionFactory;

    public function __construct(Context                       $context,
                                Registry                      $registry,
                                OrderRepository               $orderRepository,
                                ShipmentItemCollectionFactory $shipmentItemCollectionFactory,
                                TrackCollectionFactory        $trackCollectionFactory,
                                AbstractResource              $resource = null,
                                AbstractDb                    $resourceCollection = null,
                                array                         $data = [])
    {
        $this->orderRepository = $orderRepository;
        $this->shipmentItemCollectionFactory = $shipmentItemCollectionFactory;
        $this->_trackCollectionFactory = $trackCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function getShipmentStatus()
    {
        return $this->_getData(ShipmentInterface::SHIPMENT_STATUS);
    }

    public function getBillingAddressId()
    {
        return $this->_getData(ShipmentInterface::BILLING_ADDRESS_ID);
    }

    public function getCreatedAt()
    {
        return $this->_getData(ShipmentInterface::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(ShipmentInterface::CREATED_AT, $createdAt);
    }

    public function getIncrementId()
    {
        return $this->_getData(ShipmentInterface::INCREMENT_ID);
    }


    /**
     * Retrieves all available tracks in the collection that aren't deleted
     *
     * @return array
     */
    public function getAllTracks()
    {
        $tracks = [];

        foreach ($this->getTracksCollection() as $track) {

            $tracks[] = $track;

        }
        return $tracks;
    }

    /**
     * Retrieve shipping address
     *
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    public function getOrder()
    {
        if (!($this->_order instanceof Order)) {
            $this->_order = $this->orderRepository->getById($this->getOrderId());
        }
        return $this->_order;
    }

    public function getOrderId()
    {
        return $this->_getData(ShipmentInterface::ORDER_ID);
    }

    /**
     * Retrieve billing address
     *
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    public function getShippingAddressId()
    {
        return $this->getData(ShipmentInterface::SHIPPING_ADDRESS_ID);
    }

    public function getUpdatedAt()
    {
        return $this->getData(ShipmentInterface::UPDATED_AT);
    }

    /**
     * Addes a track to the collection and associates the shipment to the track
     *
     * @param Track $track
     * @return $this
     * @throws Exception
     */
    public function addTrack(Track $track)
    {
        $track->setShipment($this)
            ->setParentId($this->getId())
            ->setOrderId($this->getOrderId())
            ->setStoreId($this->getStoreId());

        if (!$track->getId()) {
            $this->getTracksCollection()->addItem($track);
        }

        $tracks = $this->getTracks();
        // as it's a new track entity, the collection doesn't contain it
        $tracks[] = $track;
        $this->setTracks($tracks);

        /**
         * Track saving is implemented in _afterSave()
         * This enforces \Magento\Framework\Model\AbstractModel::save() not to skip _afterSave()
         */
        $this->_hasDataChanges = true;

        return $this;
    }

    /**
     * Retrieve tracks collection.
     *
     * @return Collection
     */
    public function getTracksCollection()
    {
        if ($this->tracksCollection === null) {
            $this->tracksCollection = $this->_trackCollectionFactory->create();

            $id = $this->getId() ?: 0;
            $this->tracksCollection->setShipmentFilter($id);

            foreach ($this->tracksCollection as $item) {
                $item->setShipment($this);
            }
        }

        return $this->tracksCollection;

    }

    public function getTracks()
    {
        if (!$this->getId()) {
            return $this->getData(ShipmentInterface::TRACKS);
        }

        if ($this->getData(ShipmentInterface::TRACKS) === null) {
            $this->setData(ShipmentInterface::TRACKS, $this->getTracksCollection()->getItems());
        }
        return $this->getData(ShipmentInterface::TRACKS) ?? [];
    }

    public function setTracks($tracks)
    {
        return $this->setData(ShipmentInterface::TRACKS, $tracks);
    }

    public function setOrderId($id)
    {
        return $this->setData(ShipmentInterface::ORDER_ID, $id);
    }

    public function setShippingAddressId($id)
    {
        return $this->setData(ShipmentInterface::SHIPPING_ADDRESS_ID, $id);
    }

    public function setBillingAddressId($id)
    {
        return $this->setData(ShipmentInterface::BILLING_ADDRESS_ID, $id);
    }

    public function setShipmentStatus($shipmentStatus)
    {
        return $this->setData(ShipmentInterface::SHIPMENT_STATUS, $shipmentStatus);
    }

    public function setIncrementId($id)
    {
        return $this->setData(ShipmentInterface::INCREMENT_ID, $id);
    }

    public function setUpdatedAt($timestamp)
    {
        return $this->setData(ShipmentInterface::UPDATED_AT, $timestamp);
    }

    /**
     * Retrieves all non-deleted items from the shipment
     *
     * @return array
     */
    public function getAllItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {

            $items[] = $item;

        }
        return $items;
    }

    /**
     * Retrieves the collection used to track the shipment's items
     *
     * @return mixed
     */
    public function getItemsCollection()
    {
        if (!$this->hasData(ShipmentInterface::ITEMS)) {
            $this->setItems($this->shipmentItemCollectionFactory->create()->setShipmentFilter($this->getId()));

            if ($this->getId()) {
                foreach ($this->getItems() as $item) {
                    $item->setShipment($this);
                }
            }
        }

        return $this->getItems();
    }

    public function setItems($items)
    {
        return $this->setData(ShipmentInterface::ITEMS, $items);
    }

    public function getItems()
    {
        if ($this->getData(ShipmentInterface::ITEMS) === null) {
            $collection = $this->shipmentItemCollectionFactory->create()->setShipmentFilter($this->getId());
            if ($this->getId()) {
                foreach ($collection as $item) {
                    $item->setShipment($this);
                }
                $this->setData(ShipmentInterface::ITEMS, $collection->getItems());
            }
        }
        $shipmentItems = $this->getData(ShipmentInterface::ITEMS);
        if ($shipmentItems !== null && !is_array($shipmentItems)) {
            $shipmentItems = $shipmentItems->getItems();
        }
        return $shipmentItems ?? [];
    }

    /**
     * Retrieves an item from the shipment using its ID
     *
     * @param string|int $itemId
     * @return bool|\Onecode\ShopFlixConnector\Model\Order\Shipment\Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Adds an item to the shipment
     *
     * @param \Onecode\ShopFlixConnector\Model\Order\Shipment\Item $item
     * @return $this
     */
    public function addItem(\Onecode\ShopFlixConnector\Model\Order\Shipment\Item $item)
    {
        $item->setShipment($this)->setParentId($this->getId())->setStoreId($this->getStoreId());
        if (!$item->getId()) {
            $this->setItems(array_merge(
                $this->getItems() ?? [],
                [$item]
            ));
        }
        return $this;
    }

    protected function _construct()
    {
        $this->setIdFieldName(ShipmentInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }
}
