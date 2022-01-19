<?php
/**
 * Items.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\Reject\Create;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Block\Adminhtml\Items\AbstractItems;
use Onecode\ShopFlixConnector\Helper\Data;
use Onecode\ShopFlixConnector\Model\Order;

class Items extends AbstractItems
{
    /**
     * Disable submit button
     *
     * @var bool
     */
    protected $_disableSubmitButton = false;
    /**
     * @var Data
     */
    private $_shopflixData;


    /**
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param Data $shopflixData
     * @param array $data
     */
    public function __construct(
        Context                     $context,
        StockRegistryInterface      $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry                    $registry,
        Data                        $shopflixData,
        array                       $data = []
    )
    {
        $this->_shopflixData = $shopflixData;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }

    /**
     * @return Order
     * @throws LocalizedException
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    public function getOrderTotalData()
    {
        return [];
    }

    public function getOrderTotalbarData()
    {
        $this->setPriceDataObject($this->getOrder());

        $totalbarData = [];
        $totalbarData[] = [__('Paid Amount'), $this->displayPriceAttribute('amount_paid'), false];
        $totalbarData[] = [__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true];
        return $totalbarData;
    }

    public function formatPrice($price)
    {
        return $this->getOrder()->formatPrice($price);
    }

    /**
     * Get is submit button disabled or not
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisableSubmitButton()
    {
        return $this->_disableSubmitButton;
    }

    protected function _beforeToHtml()
    {
        $this->_disableSubmitButton = false;
        $_submitLabel = __('Submit Rejection');
        $this->addChild(
            'submit_button',
            Button::class,
            [
                'label' => $_submitLabel,
                'class' => 'save submit-button primary',
                'onclick' => 'disableElements(\'submit-button\');jQuery(\'.order-reject-edit\').submit()',
                'disabled' => $this->_disableSubmitButton
            ]
        );

        return parent::_prepareLayout();
    }

}
