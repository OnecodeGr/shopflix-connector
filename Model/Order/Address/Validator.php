<?php
/**
 * Validator.php
 *
 * @copyright Copyright © 2021   All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Address;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\CountryFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ObjectManager;
use Onecode\ShopFlixConnector\Model\Order\Address;

class Validator
{
    /**
     * @var array
     */
    protected $required = [
        'parent_id' => 'Parent Order Id',
        'postcode' => 'Zip code',
        'lastname' => 'Last name',
        'street' => 'Street',
        'city' => 'City',
        'email' => 'Email',
        'country_id' => 'Country',
        'firstname' => 'First Name',
        'address_type' => 'Address Type',
    ];

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @param DirectoryHelper $directoryHelper
     * @param CountryFactory $countryFactory
     * @param EavConfig $eavConfig
     */
    public function __construct(
        DirectoryHelper $directoryHelper,
        CountryFactory  $countryFactory,
        EavConfig       $eavConfig = null
    )
    {
        $this->directoryHelper = $directoryHelper;
        $this->countryFactory = $countryFactory;
        $this->eavConfig = $eavConfig ?: ObjectManager::getInstance()
            ->get(EavConfig::class);
    }

    /**
     * Validate address.
     *
     * @param Address $address
     * @return array
     */
    public function validate(Address $address)
    {
        $warnings = [];
        $this->required['telephone'] = 'Phone Number';
        foreach ($this->required as $code => $label) {
            if (!$address->hasData($code)) {
                $warnings[] = sprintf('"%s" is required. Enter and try again.', $label);
            }
        }
        if (!filter_var($address->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $warnings[] = 'Email has a wrong format';
        }
        if (!filter_var(in_array($address->getAddressType(), [Address::TYPE_BILLING, Address::TYPE_SHIPPING]))) {
            $warnings[] = 'Address type doesn\'t match required options';
        }
        return $warnings;
    }


    /**
     * Check if value is empty
     *
     * @param mixed $value
     * @return bool
     */
    protected
    function isEmpty($value)
    {
        return empty($value);
    }


}
