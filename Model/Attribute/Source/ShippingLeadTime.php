<?php
/**
 * ShippingLeadTime.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ShippingLeadTime extends AbstractSource
{

    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Same day'), 'value' => 0],
                ['label' => __('Next day'), 'value' => 1],
                ['label' => __('2 days'), 'value' => 2],
                ['label' => __('3 days'), 'value' => 3],
                ['label' => __('4 days'), 'value' => 4],
                ['label' => __('5 days'), 'value' => 5],
                ['label' => __('6 days'), 'value' => 6],
                ['label' => __('7+ days'), 'value' => 7],
            ];
        }
        return $this->_options;
    }
}
