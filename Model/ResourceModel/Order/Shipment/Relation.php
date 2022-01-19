<?php
/**
 * Relation.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
use Onecode\ShopFlixConnector\Model\Order\Shipment;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Item as ShipmentItemResource;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Track as ShipmentTrackResource;

class Relation implements RelationInterface
{

    /**
     * @var ShipmentItemResource
     */
    protected $shipmentItemResource;

    /**
     * @var ShipmentTrackResource
     */
    protected $shipmentTrackResource;



    /**
     * @param Item $shipmentItemResource
     * @param Track $shipmentTrackResource
     */
    public function __construct(
        ShipmentItemResource    $shipmentItemResource,
        ShipmentTrackResource   $shipmentTrackResource
    )
    {
        $this->shipmentItemResource = $shipmentItemResource;
        $this->shipmentTrackResource = $shipmentTrackResource;
    }

    public function processRelation(AbstractModel $object)
    {
        /** @var Shipment $object */
        if (null !== $object->getItems()) {
            foreach ($object->getItems() as $item) {
                $item->setParentId($object->getId());
                $this->shipmentItemResource->save($item);
            }
        }
        if (null !== $object->getTracks()) {
            foreach ($object->getTracks() as $track) {
                $track->setParentId($object->getId());
                $this->shipmentTrackResource->save($track);
            }
        }
    }
}
