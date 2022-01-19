<?php
/**
 * EntityAbstract.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */
declare(strict_types=1);

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

abstract class EntityAbstract extends AbstractDb
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_resource';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'resource';

    /**
     * Use additional is object new check for this resource
     *
     * @var bool
     */
    protected $_useIsObjectNew = true;


    /**
     * Prepares data for saving and removes update time (if exists).
     *
     * This prevents saving same update time on each entity update.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    protected function _prepareDataForSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data = parent::_prepareDataForTable($object, $this->getMainTable());

        if (isset($data['updated_at'])) {
            unset($data['updated_at']);
        }

        return $data;
    }

    /**
     * Perform actions before object save
     *
     * Perform actions before object save, calculate next sequence value for increment Id
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $columns = $connection->describeTable($this->getMainTable());

        if (isset($columns['created_at'], $columns['updated_at'])) {
            $select = $connection->select()
                ->from($this->getMainTable(), ['created_at', 'updated_at'])
                ->where($this->getIdFieldName() . ' = :entity_id');
            $row = $connection->fetchRow($select, [':entity_id' => $object->getId()]);

            if (is_array($row) && isset($row['created_at'], $row['updated_at'])) {
                $object->setCreatedAt($row['created_at']);
                $object->setUpdatedAt($row['updated_at']);
            }
        }

        parent::_afterSave($object);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function updateObject(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = $this->getConnection()->quoteInto($this->getIdFieldName() . '=?', $object->getId());
        $data = $this->_prepareDataForSave($object);
        unset($data[$this->getIdFieldName()]);
        $this->getConnection()->update($this->getMainTable(), $data, $condition);
    }

    /**
     * @inheritdoc
     */
    protected function saveNewObject(\Magento\Framework\Model\AbstractModel $object)
    {
        $bind = $this->_prepareDataForSave($object);
        unset($bind[$this->getIdFieldName()]);
        $this->getConnection()->insert($this->getMainTable(), $bind);
        $object->setId($this->getConnection()->lastInsertId($this->getMainTable()));
        if ($this->_useIsObjectNew) {
            $object->isObjectNew(false);
        }
    }

    /**
     * Perform actions after object delete
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterDelete($object);
        return $this;
    }

}
