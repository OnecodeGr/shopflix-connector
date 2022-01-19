<?php
/**
 * AbstractOrder.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\Order;

class AbstractOrder extends Widget
{

    protected $_coreRegistry = null;
    protected $_adminHelper;

    public function __construct(
        Context  $context,
        Registry $registry,
        Admin    $adminHelper,
        array    $data = []
    )
    {
        $this->_adminHelper = $adminHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Display price attribute
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    /**
     * Get price data object
     *
     * @return Order|mixed
     * @throws LocalizedException
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getOrder();
        }
        return $obj;
    }

    /**
     * Retrieve available order
     *
     * @return Order
     * @throws LocalizedException
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if ($this->_coreRegistry->registry('onecode_shopflix_order')) {
            return $this->_coreRegistry->registry('onecode_shopflix_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new LocalizedException(__('We can\'t get the order instance right now.'));
    }

    /**
     * Display prices
     *
     * @param float $basePrice
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPrices(
            $this->getPriceDataObject(),
            $basePrice,
            $price,
            $strong,
            $separator
        );
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return [];
    }


    /**
     * Retrieve order info block settings
     *
     * @return array
     */
    public function getOrderInfoData()
    {
        return [];
    }
}
