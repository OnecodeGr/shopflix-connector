<?php
/**
 * Item.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment;

use Onecode\ShopFlixConnector\Model\ResourceModel\Order\EntityAbstract;
use Onecode\ShopFlixConnector\Model\Spi\ShipmentItemResourceInterface;

class Item extends EntityAbstract implements ShipmentItemResourceInterface
{

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_shipment_item_resource';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('onecode_shopflix_shipment_item', 'entity_id');
    }
    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \Onecode\ShopFlixConnector\Model\Order\Shipment\Item $object */
        if (!$object->getParentId() && $object->getShipment()) {
            $object->setParentId($object->getShipment()->getId());
        }

        return parent::_beforeSave($object);
    }
}
