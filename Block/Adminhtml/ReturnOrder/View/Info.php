<?php
/**
 * Info.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\View;

use DateTime;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\AbstractOrder;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Address;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Address\Renderer;

/**
 * @method setOrder($order)
 */
class Info extends AbstractOrder
{
    private $addressRenderer;


    public function __construct(Context                   $context,
                                Registry                  $registry,
                                Admin                     $adminHelper,
                                Renderer                  $addressRenderer,
                                array                     $data = [])
    {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * Get order view URL.
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl(int $orderId): string
    {
        return $this->getUrl('shopflix/returnorder/view', ['order_id' => $orderId]);
    }



    public function getShopflixParentOrder($orderId): string
    {
        return $this->getUrl('shopflix/order/view', ['order_id' => $orderId]);
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     *
     * @return bool
     */
    public function shouldDisplayCustomerIp(): bool
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            ScopeInterface::SCOPE_STORE,
            null
        );
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, int $sortOrder): int
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
    public function getCreatedAtStoreDate($store, string $createdAt): DateTime
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * Get timezone for store
     *
     * @param mixed $store
     * @return string
     */
    public function getTimezoneForStore($store): string
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
    public function getFormattedAddress(Address $address): ?string
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

    /**
     * Get object created at date
     *
     * @param string $createdAt
     * @return \DateTime
     * @throws \Exception
     */
    public function getOrderAdminDate(string $createdAt): DateTime
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

}
