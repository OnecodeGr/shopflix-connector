<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Onecode\ShopFlixConnector\Model\Spi\ReturnOrderItemResourceInterface;

class Item extends AbstractDb implements ReturnOrderItemResourceInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_item_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('onecode_shopflix_return_order_item', 'entity_id');
        $this->_useIsObjectNew = true;
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws NoSuchEntityException
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
