<?php
/**
 * Totals.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder;

use Magento\Framework\DataObject;
use Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrderTotals;

class Totals extends ReturnOrderTotals
{


    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals(): Totals
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
