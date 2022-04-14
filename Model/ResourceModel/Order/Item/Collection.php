<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item;

use Onecode\ShopFlixConnector\Api\Data\ItemSearchResultInterface;
use Onecode\ShopFlixConnector\Model\Order\Item as Model;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item as ResourceModel;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Collection\AbstractCollection;

class Collection extends AbstractCollection implements ItemSearchResultInterface
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /**
     * Set random items order
     *
     * @return $this
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
     * @return $this
     */
    public function addIdFilter($item): Collection
    {
        if (is_array($item)) {
            $this->addFieldToFilter('item_id', ['in' => $item]);
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
     * @param int $parentId
     * @return $this
     */
    public function setOrderFilter($parentId = null): Collection
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
     * @param int $parentId
     * @return $this
     */
    public function filterByParent($parentId = null): Collection
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
     * @return $this
     */
    public function filterByTypes($typeIds): Collection
    {
        $this->addFieldToFilter('product_type', ['in' => $typeIds]);
        return $this;
    }

    /**
     * Assign parent items on after collection load
     *
     * @return $this
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
