<?php
/**
 * Options.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Ui\Component\ReturnOrder\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusInterface;

class Options implements OptionSourceInterface
{
    protected $options = [];

    public function toOptionArray(): array
    {
        $this->options = [
            [
                "value" => ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED,
                "label" => __("Return Requested")
            ],
            [
                "value" => ReturnOrderStatusInterface::STATUS_RETURN_APPROVED,
                "label" => __("Approved")
            ],
            [
                "value" => ReturnOrderStatusInterface::STATUS_RETURN_DECLINED,
                "label" => __("Declined")
            ],
            [
                "value" => ReturnOrderStatusInterface::STATUS_RETURN_COMPLETED,
                "label" => __("Completed")
            ],
            [
                "value" => ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE,
                "label" => __("Delivered to the store")
            ],
            [
                "value" => ReturnOrderStatusInterface::STATUS_ON_THE_WAY_TO_THE_STORE,
                "label" => __("On the way to the store")
            ],
        ];

        return $this->options;
    }


}
