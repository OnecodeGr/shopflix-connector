<?php
/**
 * AbstractCollection.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Collection;

use Exception;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection;

abstract class AbstractCollection
    extends Collection
{
    /**
     * @var Select
     */
    protected $_countSelect;

    /**
     * @var SearchCriteriaInterface
     */
    protected $searchCriteria;


    /**
     * get select count sql
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        if (!$this->_countSelect instanceof Select) {
            $this->setSelectCountSql(parent::getSelectCountSql());
        }
        return $this->_countSelect;
    }

    /**
     * Set select count sql
     *
     * @param Select $countSelect
     * @return $this
     */
    public function setSelectCountSql(Select $countSelect)
    {
        $this->_countSelect = $countSelect;
        return $this;
    }

    /**
     * Add attribute to select result set.
     * Backward compatibility with EAV collection
     *
     * @param string $attribute
     * @return $this
     */
    public function addAttributeToSelect($attribute)
    {
        $this->addFieldToSelect($this->_attributeToField($attribute));
        return $this;
    }

    /**
     * Check if $attribute is \Magento\Eav\Model\Entity\Attribute and convert to string field name
     *
     * @param string|Attribute $attribute
     * @return string
     * @throws LocalizedException
     */
    protected function _attributeToField($attribute)
    {
        $field = false;
        if (is_string($attribute)) {
            $field = $attribute;
        } elseif ($attribute instanceof Attribute) {
            $field = $attribute->getAttributeCode();
        }
        if (!$field) {
            throw new LocalizedException(__('We cannot determine the field name.'));
        }
        return $field;
    }

    /**
     * Specify collection select filter by attribute value
     * Backward compatibility with EAV collection
     *
     * @param string|Attribute $attribute
     * @param array|int|string|null $condition
     * @return $this
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        $this->addFieldToFilter($this->_attributeToField($attribute), $condition);
        return $this;
    }

    /**
     * Specify collection select order by attribute value
     * Backward compatibility with EAV collection
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir = 'asc')
    {
        $this->addOrder($this->_attributeToField($attribute), $dir);
        return $this;
    }

    /**
     * Set collection page start and records to show
     * Backward compatibility with EAV collection
     *
     * @param int $pageNum
     * @param int $pageSize
     * @return $this
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)->setPageSize($pageSize);
        return $this;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Create all ids retrieving select with limitation
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }

    /**
     * Retrieve collection items
     *
     * @return DataObject[]
     */
    public function getItems()
    {
        $this->load();
        return $this->_items;
    }

    /**
     * Get search criteria.
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        $this->_totalRecords = $totalCount;
        return $this;
    }

    /**
     * Set items list.
     *
     * @param ExtensibleDataInterface[] $items
     * @return $this
     * @throws Exception
     */
    public function setItems(array $items = null)
    {
        if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }


}
