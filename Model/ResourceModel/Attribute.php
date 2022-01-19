<?php
/**
 * Attribute.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel;

use Exception;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Model\AbstractModel;

class Attribute
{
    /**
     * @var AppResource
     */
    protected $resource;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @param AppResource $resource
     * @param EventManager $eventManager
     */
    public function __construct(
        AppResource  $resource,
        EventManager $eventManager
    )
    {
        $this->resource = $resource;
        $this->eventManager = $eventManager;
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel $object
     * @param string $attribute
     * @return \Onecode\ShopFlixConnector\Model\ResourceModel\Attribute
     * @throws Exception
     */
    public function saveAttribute(AbstractModel $object, $attribute)
    {
        if ($attribute instanceof AbstractAttribute) {
            $attributes = $attribute->getAttributeCode();
        } elseif (is_string($attribute)) {
            $attributes = [$attribute];
        } else {
            $attributes = $attribute;
        }
        if (is_array($attributes) && !empty($attributes)) {
            $this->getConnection()->beginTransaction();
            $data = array_intersect_key($object->getData(), array_flip($attributes));
            try {
                $this->_beforeSaveAttribute($object, $attributes);
                if ($object->getId() && !empty($data)) {
                    $this->getConnection()->update(
                        $object->getResource()->getMainTable(),
                        $data,
                        [$object->getResource()->getIdFieldName() . '= ?' => (int)$object->getId()]
                    );
                    $object->addData($data);
                }
                $this->_afterSaveAttribute($object, $attributes);
                $this->getConnection()->commit();
            } catch (Exception $e) {
                $this->getConnection()->rollBack();
                throw $e;
            }
        }
        return $this;
    }

    /**
     * @return AdapterInterface
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->resource->getConnection('onecode_shopflix');
        }
        return $this->connection;
    }

    /**
     * Before save object attribute
     *
     * @param AbstractModel $object
     * @param string $attribute
     * @return \Onecode\ShopFlixConnector\Model\ResourceModel\Attribute
     */
    protected function _beforeSaveAttribute(AbstractModel $object, $attribute)
    {
        if ($object->getEventObject() && $object->getEventPrefix()) {
            $this->eventManager->dispatch(
                $object->getEventPrefix() . '_save_attribute_before',
                [
                    $object->getEventObject() => $this,
                    'object' => $object,
                    'attribute' => $attribute
                ]
            );
        }
        return $this;
    }

    /**
     * After save object attribute
     *
     * @param AbstractModel $object
     * @param string $attribute
     * @return \Onecode\ShopFlixConnector\Model\ResourceModel\Attribute
     */
    protected function _afterSaveAttribute(AbstractModel $object, $attribute)
    {
        if ($object->getEventObject() && $object->getEventPrefix()) {
            $this->eventManager->dispatch(
                $object->getEventPrefix() . '_save_attribute_after',
                [
                    $object->getEventObject() => $this,
                    'object' => $object,
                    'attribute' => $attribute
                ]
            );
        }
        return $this;
    }
}
