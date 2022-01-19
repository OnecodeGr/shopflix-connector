<?php
/**
 * Totals.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\Reject;

use Magento\Framework\DataObject;

class Totals extends \Onecode\ShopFlixConnector\Block\Adminhtml\Totals
{

    /**
     * @return $this|Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['paid'] = new DataObject(
            [
                'code' => 'paid',
                'strong' => true,
                'value' => $this->getSource()->getTotalPaid(),
                'base_value' => $this->getSource()->getTotalPaid(),
                'label' => __('Total Paid'),
                'area' => 'footer',
            ]
        );
        return $this;
    }
}
