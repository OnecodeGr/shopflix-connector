<?php
/**
 * AbstractOrder.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\Order;

/**
 * @method hasOrder()
 */
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
     * @return OrderInterface
     * @throws LocalizedException
     */
    public function getOrder(): OrderInterface
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
     * @throws LocalizedException
     */
    public function displayPrices(float $basePrice, float $price, bool $strong = false, string $separator = '<br/>'): string
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
    public function getOrderTotalData(): array
    {
        return [];
    }


    /**
     * Retrieve order info block settings
     *
     * @return array
     */
    public function getOrderInfoData(): array
    {
        return [];
    }
}
