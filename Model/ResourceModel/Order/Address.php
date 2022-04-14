<?php
/**
 * Address.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Onecode\ShopFlixConnector\Model\Order\Address\Validator;
use Onecode\ShopFlixConnector\Model\Spi\AddressResourceInterface;

class Address extends EntityAbstract implements AddressResourceInterface
{

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_address_resource';

    /**
     * @var Validator
     */
    private $_validator;

    public function __construct(Context $context, Snapshot $entitySnapshot, RelationComposite $entityRelationComposite, Validator $validator, $connectionName = null)
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

    protected function _construct()
    {
        $this->_init('onecode_shopflix_order_address', 'entity_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object): AbstractDb
    {
        /**@var $object \Onecode\ShopFlixConnector\Model\Order\Address */
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
