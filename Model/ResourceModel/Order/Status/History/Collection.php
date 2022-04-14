<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status\History;

use Onecode\ShopFlixConnector\Api\Data\StatusHistorySearchResultInterface;
use Onecode\ShopFlixConnector\Model\Order\Status\History as HistoryModel;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status\History as HistoryResource;

class Collection extends AbstractCollection implements StatusHistorySearchResultInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_status_history_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'onecode_shopflix_order_status_history_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            HistoryModel::class,
            HistoryResource::class
        );
    }
}
