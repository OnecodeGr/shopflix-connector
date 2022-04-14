<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Item;

use Onecode\ShopFlixConnector\Api\Data\ShipmentItemSearchResultInterface;
use Onecode\ShopFlixConnector\Model\Order\Shipment\Item as Model;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Item as ResourceModel;

class Collection extends AbstractCollection implements ShipmentItemSearchResultInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_shipment_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_shipment_item_collection';

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentFilter($shipmentId): Collection
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
