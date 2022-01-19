<?php
/**
 * Collection
 *
 * @copyright Copyright © 2021 Onecode All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Helper;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Onecode\ShopFlixConnector\Api\Data\OrderSearchResultInterface;
use Onecode\ShopFlixConnector\Model\Order as Model;
use Onecode\ShopFlixConnector\Model\ResourceModel\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order as ResourceModel;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection implements OrderSearchResultInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_collection';
    private $_coreResourceHelper;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface        $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface       $eventManager,
        Snapshot               $entitySnapshot,
        Helper                 $coreResourceHelper,
        AdapterInterface       $connection = null,
        AbstractDb             $resource = null
    )
    {
        $this->_coreResourceHelper = $coreResourceHelper;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * @return $this
     *
     */
    public function addAddressFields()
    {
        return $this->_addAddressFields();
    }

    /**
     * Join table onecode_shopflix_address to select for billing and shipping order addresses.
     *
     * Create correlation map
     *
     * @return $this
     */
    protected function _addAddressFields()
    {
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $joinTable = $this->getTable('onecode_shopflix_address');

        $this->addFilterToMap(
            'billing_firstname',
            $billingAliasName . '.firstname'
        )->addFilterToMap(
            'billing_lastname',
            $billingAliasName . '.lastname'
        )->addFilterToMap(
            'billing_telephone',
            $billingAliasName . '.telephone'
        )->addFilterToMap(
            'billing_postcode',
            $billingAliasName . '.postcode'
        )->addFilterToMap(
            'shipping_firstname',
            $shippingAliasName . '.firstname'
        )->addFilterToMap(
            'shipping_lastname',
            $shippingAliasName . '.lastname'
        )->addFilterToMap(
            'shipping_telephone',
            $shippingAliasName . '.telephone'
        )->addFilterToMap(
            'shipping_postcode',
            $shippingAliasName . '.postcode'
        );

        $this->getSelect()->joinLeft(
            [$billingAliasName => $joinTable],
            "(main_table.entity_id = {$billingAliasName}.parent_id" .
            " AND {$billingAliasName}.address_type = 'billing')",
            [
                $billingAliasName . '.firstname',
                $billingAliasName . '.lastname',
                $billingAliasName . '.telephone',
                $billingAliasName . '.postcode'
            ]
        )->joinLeft(
            [$shippingAliasName => $joinTable],
            "(main_table.entity_id = {$shippingAliasName}.parent_id" .
            " AND {$shippingAliasName}.address_type = 'shipping')",
            [
                $shippingAliasName . '.firstname',
                $shippingAliasName . '.lastname',
                $shippingAliasName . '.telephone',
                $shippingAliasName . '.postcode'
            ]
        );
        $this->_coreResourceHelper->prepareColumnsList($this->getSelect());
        return $this;
    }

    /**
     * Specify collection select filter by attribute value
     *
     * @param array $attributes
     * @param array|int|string|null $condition
     * @return $this
     */
    public function addAttributeToSearchFilter($attributes, $condition = null)
    {
        if (is_array($attributes) && !empty($attributes)) {
            $this->_addAddressFields();

            foreach ($attributes as $attribute) {
                $this->addFieldToSearchFilter($this->_attributeToField($attribute['attribute']), $attribute);
            }
        } else {
            $this->addAttributeToFilter($attributes, $condition);
        }

        return $this;
    }

    /**
     * Add field search filter to collection as OR condition
     *
     * @param string $field
     * @param int|string|array|null $condition
     * @return $this
     *
     * @see self::_getConditionSql for $condition
     */
    public function addFieldToSearchFilter($field, $condition = null)
    {
        $field = $this->_getMappedField($field);
        $this->_select->orWhere($this->_getConditionSql($field, $condition));
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

    public function getSelectCountSql()
    {
        /* @var $countSelect Select */
        $countSelect = parent::getSelectCountSql();
        $countSelect->resetJoinLeft();
        return $countSelect;
    }

    public function addItemCountExpr()
    {
        if ($this->_fieldsToSelect === null) {
            // If we select all fields from table, we need to add column alias
            $this->getSelect()->columns(['items_count' => 'total_item_count']);
        } else {
            $this->addFieldToSelect('total_item_count', 'items_count');
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);


    }

    /**
     * Reset left join
     *
     * @param int $limit
     * @param int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = parent::_getAllIdsSelect($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $idsSelect;
    }
}
