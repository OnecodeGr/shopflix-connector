<?php
/**
 * TrackingVoucherFormat.php
 *
 * @copyright Copyright © 2022 Onecode P.C  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class TrackingVoucherFormat implements OptionSourceInterface
{

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            "clean" => __("Courier Center Standard"),
            "pdf" => __("Courier Center Labeled"),
            "singleclean" => __("Courier Center Standard (1 tracking voucher per page)"),
            "singlepdf" => __("Courier Center Labeled ( 1 tracking voucher per page )"),
            "singlepdf_100x150" => __("SHOPFLIX Labeled 100x150"),
            "singlepdf_100x170" => __("SHOPFLIX labeled 100x170")
        ];
    }
}
