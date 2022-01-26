<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Track;

use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackSearchResultInterface;
use Onecode\ShopFlixConnector\Model\Order\Shipment\Track as Model;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Track as ResourceModel;

class Collection extends AbstractCollection implements ShipmentTrackSearchResultInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_shipment_track_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_shipment_track_collection';

    /**
     * Order field
     *
     * @var string
     */
    protected $_orderField = 'order_id';

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentFilter($shipmentId)
    {
        $this->addFieldToFilter('parent_id', $shipmentId);
        return $this;
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Model::class,
            ResourceModel::class
        );
    }
}
