<?php
/**
 * Item.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Onecode\ShopFlixConnector\Model\Spi\ItemResourceInterface;


class Item extends AbstractDb implements ItemResourceInterface
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('onecode_shopflix_order_item', 'item_id');
    }


    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object): AbstractDb
    {
        /**@var $object \Onecode\ShopFlixConnector\Model\Order\Item */
        if (!$object->getOrderId() && $object->getOrder()) {
            $object->setOrderId($object->getOrder()->getId());
        }

        return parent::_beforeSave($object);
    }

}
