<?php
/**
 * IsShopFlix.php
 *
 * @copyright Copyright © 2022 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Ui\Component\Order\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

class IsShopFlix implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                "value" => 1,
                "label" => __("Yes")
            ],
            [
                "value" => 0,
                "label" => __("No")
            ],
        ];
    }
}
