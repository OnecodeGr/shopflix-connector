<?php
/**
 * Shipment.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order;

use Onecode\ShopFlixConnector\Model\Spi\ShipmentResourceInterface;

class Shipment extends EntityAbstract implements ShipmentResourceInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_shipment_resource';

    protected function _construct()
    {
        $this->_init('onecode_shopflix_shipment', 'entity_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \Onecode\ShopFlixConnector\Model\Order\Shipment $object */
        if ((!$object->getId() || null !== $object->getItems()) && !count($object->getAllItems())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot create an empty shipment.'));
        }

        if (!$object->getOrderId() && $object->getOrder()) {
            $object->setOrderId($object->getOrder()->getId());
            $object->setShippingAddressId($object->getOrder()->getShippingAddress()->getId());
        }

        return parent::_beforeSave($object);
    }
}
