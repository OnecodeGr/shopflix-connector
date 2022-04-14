<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Address\Validator;
use Onecode\ShopFlixConnector\Model\Spi\ReturnOrderAddressResourceInterface;


class Address extends EntityAbstract implements ReturnOrderAddressResourceInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_address_resource_model';
    /**
     * @var Validator
     */
    private $_validator;

    public function __construct(
        Context           $context,
        Snapshot          $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Validator         $validator,
                          $connectionName = null
    )
    {
        $this->_validator = $validator;
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }

    /**
     * Return configuration for all attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributes = [
            'city' => __('City'),
            'company' => __('Company'),
            'country_id' => __('Country'),
            'email' => __('Email'),
            'firstname' => __('First Name'),
            'lastname' => __('Last Name'),
            'region_id' => __('State/Province'),
            'street' => __('Street Address'),
            'telephone' => __('Phone Number'),
            'postcode' => __('Zip/Postal Code'),
        ];
        asort($attributes);
        return $attributes;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('onecode_shopflix_return_order_address', 'entity_id');
        $this->_useIsObjectNew = true;
    }

    /**
     * @param AbstractModel $object
     *
     *
     * @return Address
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /**@var $object \Onecode\ShopFlixConnector\Model\ReturnOrder\Address */
        if (!$object->getParentId() && $object->getOrder()) {
            $object->setParentId($object->getOrder()->getId());
        }
        $warnings = $this->_validator->validate($object);
        if (!empty($warnings)) {
            throw new LocalizedException(
                __("We can't save the address:\n%1", implode("\n", $warnings))
            );
        }


        return parent::_beforeSave($object);
    }
}
