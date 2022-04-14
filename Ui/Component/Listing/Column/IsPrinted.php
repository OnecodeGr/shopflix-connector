<?php
/**
 * IsPrinted.php
 *
 * @copyright Copyright © 2022 Onecode P.C  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

class IsPrinted implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            ["value" => 0, "label" => __("No")],
            ["value" => 1, "label" => __("Yes")],
        ];
    }
}
