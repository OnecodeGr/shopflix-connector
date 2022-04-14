<?php
/**
 * Mofidiers.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class TimeModifiers implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
          /**  Due to an error on SHOPFLIX API supporting only -7 days as modifier.
            ['value' => "-6 hours", "label" => __("-6 hours")],
            ['value' => "-12 hours", "label" => __("-12 hours")],
            ['value' => "-18 hours", "label" => __("-18 hours")],
            ['value' => "-1 day", "label" => __("-1 day")],
           */
            ['value' => "-7 day", "label" => __("-7 day")],
        ];
    }
}
