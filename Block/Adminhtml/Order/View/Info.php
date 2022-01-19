<?php
/**
 * Info.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View;

use DateTime;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\Order\AbstractOrder;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\Order\Address;
use Onecode\ShopFlixConnector\Model\Order\Address\Renderer;

class Info extends AbstractOrder
{
    private $addressRenderer;
    private $metadata;
    private $_metadataElementFactory;

    public function __construct(Context                   $context,
                                Registry                  $registry,
                                Admin                     $adminHelper,
                                CustomerMetadataInterface $metadata,
                                ElementFactory            $elementFactory,
                                Renderer                  $addressRenderer,
                                array                     $data = [])
    {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->addressRenderer = $addressRenderer;
        $this->metadata = $metadata;
        $this->_metadataElementFactory = $elementFactory;
    }

    /**
     * Get order view URL.
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('shopflix/order/view', ['order_id' => $orderId]);
    }


    /**
     * Get Magento Order view URL.
     *
     * @param int $orderId
     * @return string
     */
    public function getMagentoOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     *
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            ScopeInterface::SCOPE_STORE,
            null
        );
    }

    public function getCustomerAccountData()
    {
        $accountData = [];
        $entityType = 'customer';

        /* @var AttributeMetadataInterface $attribute */
        foreach ($this->metadata->getAllAttributesMetadata($entityType) as $attribute) {
            if (!$attribute->isVisible() || $attribute->isSystem()) {
                continue;
            }
            $orderKey = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue != '') {
                $metadataElement = $this->_metadataElementFactory->create($attribute, $orderValue, $entityType);
                $value = $metadataElement->outputValue(AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortOrder = $attribute->getSortOrder() + $attribute->isUserDefined() ? 200 : 0;
                $sortOrder = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, ['br']),
                ];
            }
        }
        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->_prepareAccountDataSortOrder($data, $sortOrder + 1);
        }

        return $sortOrder;
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @param mixed $store
     * @param string $createdAt
     * @return DateTime
     */
    public function getCreatedAtStoreDate($store, $createdAt)
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * Get timezone for store
     *
     * @param mixed $store
     * @return string
     */
    public function getTimezoneForStore($store)
    {
        return $this->_localeDate->getConfigTimezone(
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new LocalizedException(
                __('Please correct the parent block for this block.')
            );
        }
        $this->setOrder($this->getParentBlock()->getOrder());

        foreach ($this->getParentBlock()->getOrderInfoData() as $key => $value) {
            $this->setDataUsingMethod($key, $value);
        }

        parent::_beforeToHtml();
    }
}
