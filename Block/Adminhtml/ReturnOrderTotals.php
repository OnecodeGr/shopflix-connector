<?php
/**
 * ReturnOrderTotals.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Onecode\ShopFlixConnector\Helper\Admin;

class ReturnOrderTotals extends \Onecode\ShopFlixConnector\Block\ReturnOrder\Totals
{
    /**
     * Admin helper
     *
     * @var Admin
     */
    protected $_adminHelper;

    public function __construct(Context $context, Registry $registry, Admin $adminHelper, array $data = [])
    {

        $this->_adminHelper = $adminHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Format total value based on order currency
     *
     * @param DataObject $total
     * @return string
     */
    public function formatValue($total):string
    {
        if (!$total->getIsFormated()) {
            return $this->_adminHelper->displayPrices($this->getOrder(), $total->getBaseValue(), $total->getValue());
        }
        return $total->getValue();
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        $this->_totals = [];
        $order = $this->getSource();

        $this->_totals['subtotal'] = new DataObject(
            [
                'code' => 'subtotal',
                'value' => $order->getSubtotal(),
                'base_value' => $order->getSubtotal(),
                'label' => __('Subtotal'),
            ]
        );


        return $this;
    }
}
