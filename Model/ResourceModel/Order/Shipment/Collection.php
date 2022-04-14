<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment;

use Onecode\ShopFlixConnector\Api\Data\ShipmentItemSearchResultInterface;
use Onecode\ShopFlixConnector\Model\Order\Shipment as Model;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment as ResourceModel;

class Collection extends AbstractCollection
    implements ShipmentItemSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_shipment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_shipment_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField = 'order_id';

    protected function _construct()
    {
        $this->_init(
            Model::class,
            ResourceModel::class
        );
    }

    /**
     * Unserialize packages in each item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        return parent::_afterLoad();
    }
}
