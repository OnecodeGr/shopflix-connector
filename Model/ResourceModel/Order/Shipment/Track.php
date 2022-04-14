<?php
/**
 * Track.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Onecode\ShopFlixConnector\Model\Order\Shipment\Track\Validator;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\EntityAbstract;
use Onecode\ShopFlixConnector\Model\Spi\ShipmentTrackResourceInterface;

class Track extends EntityAbstract implements ShipmentTrackResourceInterface
{


    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_shipment_track_resource';

    /**
     * Validator
     *
     * @var Validator
     */
    protected $validator;

    public function __construct(
        Context $context,
        Snapshot                                          $entitySnapshot,
        RelationComposite                                 $entityRelationComposite,
        Validator                                         $validator,
                                                          $connectionName = null
    )
    {
        $this->validator = $validator;
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }


    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('onecode_shopflix_shipment_track', 'entity_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        /** @var \Onecode\ShopFlixConnector\Model\Order\Shipment\Track $object */
        if (!$object->getParentId() && $object->getShipment()) {
            $object->setParentId($object->getShipment()->getId());
        }

        parent::_beforeSave($object);
        $errors = $this->validator->validate($object);
        if (!empty($errors)) {
            throw new LocalizedException(
                __("Cannot save track:\n%1", implode("\n", $errors))
            );
        }

        return $this;
    }
}
