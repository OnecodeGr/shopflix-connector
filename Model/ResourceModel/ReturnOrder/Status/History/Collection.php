<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Status\History;

use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistorySearchResultInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Status\History as HistoryResource;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Status\History as HistoryModel;

class Collection extends AbstractCollection implements ReturnOrderStatusHistorySearchResultInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_status_history_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'onecode_shopflix_return_order_status_history_collection';

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
