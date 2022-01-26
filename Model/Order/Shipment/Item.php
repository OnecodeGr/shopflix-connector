<?php
/**
 * Item.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Shipment;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\ShipmentItemInterface;
use Onecode\ShopFlixConnector\Model\Order\ItemFactory;
use Onecode\ShopFlixConnector\Model\Order\Shipment;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Item as ResourceModel;

class Item extends AbstractModel implements ShipmentItemInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_shipment_item';
    /**
     * @var string
     */
    protected $_eventObject = 'shipment_item';


    /**
     * @var Shipment|null
     */
    protected $_shipment = null;

    /**
     * @var \Onecode\ShopFlixConnector\Model\Order\Item|null
     */
    protected $_orderItem = null;

    /**
     * @var ItemFactory
     */
    protected $_orderItemFactory;

    public function __construct(
        Context          $context,
        Registry         $registry,
        ItemFactory      $orderItemFactory,
        AbstractResource $resource = null,
        AbstractDb       $resourceCollection = null,
        array            $data = []
    )
    {
        $this->_orderItemFactory = $orderItemFactory;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }


    /**
     * Retrieve order item instance
     *
     * @return \Onecode\ShopFlixConnector\Model\Order\Item
     */
    public function getOrderItem()
    {
        if (null === $this->_orderItem) {
            if ($this->getShipment()) {
                $this->_orderItem = $this->getShipment()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = $this->_orderItemFactory->create()->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Retrieve Shipment instance
     *
     * @codeCoverageIgnore
     *
     * @return Shipment
     */
    public function getShipment(): ?Shipment
    {
        return $this->_shipment;
    }

    /**
     * Declare Shipment instance
     *
     * @codeCoverageIgnore
     *
     * @param Shipment $shipment
     * @return $this
     */
    public function setShipment(Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrderItemId()
    {
        return $this->_getData(ShipmentItemInterface::ORDER_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderItemId($id)
    {
        return $this->setData(ShipmentItemInterface::ORDER_ITEM_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->_getData(ShipmentItemInterface::QTY);
    }

    /**
     * @inheritDoc
     */
    public function setQty($qty)
    {
        return $this->setData(ShipmentItemInterface::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->_getData(ShipmentItemInterface::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return $this->_getData(ShipmentItemInterface::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->_getData(ShipmentItemInterface::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getRowTotal()
    {
        return $this->_getData(ShipmentItemInterface::ROW_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->_getData(ShipmentItemInterface::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setParentId($id)
    {
        return $this->setData(ShipmentItemInterface::PARENT_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setRowTotal($amount)
    {
        return $this->setData(ShipmentItemInterface::ROW_TOTAL, $amount);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        return $this->setData(ShipmentItemInterface::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(ShipmentItemInterface::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setSku($sku)
    {
        return $this->setData(ShipmentItemInterface::SKU, $sku);
    }

    protected function _construct()
    {
        $this->setIdFieldName(ShipmentItemInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }
}
