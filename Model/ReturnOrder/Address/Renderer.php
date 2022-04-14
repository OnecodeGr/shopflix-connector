<?php
/**
 * Renderer.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder\Address;

use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Store\Model\ScopeInterface;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Address;

class Renderer
{

    /**
     * @var AddressConfig
     */
    protected $addressConfig;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor
     *
     * @param AddressConfig $addressConfig
     * @param EventManager $eventManager
     * @param ScopeConfigInterface|null $scopeConfig
     */
    public function __construct(
        AddressConfig         $addressConfig,
        EventManager          $eventManager,
        ?ScopeConfigInterface $scopeConfig = null
    )
    {
        $this->addressConfig = $addressConfig;
        $this->eventManager = $eventManager;
        $this->scopeConfig = $scopeConfig ?: ObjectManager::getInstance()->get(ScopeConfigInterface::class);
    }


    public function format(Address $address, $type)
    {

        $storeId = 0;
        $this->addressConfig->setStore(0);
        $formatType = $this->addressConfig->getFormatByCode($type);
        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }
        $this->eventManager->dispatch('customer_address_format', ['type' => $formatType, 'address' => $address]);
        $addressData = $address->getData();
        $addressData['locale'] = $this->getLocaleByStoreId((int)$storeId);

        return $formatType->getRenderer()->renderArray($addressData);
    }

    private function getLocaleByStoreId(int $storeId): string
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_DEFAULT_LOCALE, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
