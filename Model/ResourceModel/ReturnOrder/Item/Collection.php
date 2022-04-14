<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Item;

use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemSearchResultInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Item as ResourceModel;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Item as Model;

class Collection extends AbstractCollection implements ReturnOrderItemSearchResultInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_item_collection';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /**
     * Set random items order
     *
     * @return Collection
     */
    public function setRandomOrder(): Collection
    {
        $this->getConnection()->orderRand($this->getSelect());
        return $this;
    }

    /**
     * Set filter by item id
     *
     * @param mixed $item
     * @return Collection
     */
    public function addIdFilter($item): Collection
    {
        if (is_array($item)) {
            $this->addFieldToFilter("item_id", ['in' => $item]);
        } elseif ($item instanceof Model) {
            $this->addFieldToFilter('item_id', $item->getId());
        } else {
            $this->addFieldToFilter('item_id', $item);
        }
        return $this;
    }

    /**
     * Filter collection by parent_item_id
     *
     * @param int|null $parentId
     * @return Collection
     */
    public function setOrderFilter(int $parentId = null): Collection
    {
        if (empty($parentId)) {
            $this->addFieldToFilter('parent_id', ['null' => true]);
        } else {
            $this->addFieldToFilter('parent_id', $parentId);
        }
        return $this;
    }

    /**
     * Filter collection by parent_item_id
     *
     * @param int|null $parentId
     * @return Collection
     */
    public function filterByParent(int $parentId = null): Collection
    {
        if (empty($parentId)) {
            $this->addFieldToFilter('parent_item_id', ['null' => true]);
        } else {
            $this->addFieldToFilter('parent_item_id', $parentId);
        }
        return $this;
    }

    /**
     * Filter collection by specified product types
     *
     * @param array $typeIds
     * @return Collection
     */
    public function filterByTypes(array $typeIds): Collection
    {
        $this->addFieldToFilter('product_type', ['in' => $typeIds]);
        return $this;
    }


    /**
     * Assign parent items on after collection load
     *
     * @return Collection
     */
    protected function _afterLoad(): Collection
    {
        parent::_afterLoad();
        /**
         * Assign parent items
         */
        foreach ($this as $item) {
            $this->_resource->unserializeFields($item);
        }
        return $this;
    }
}
