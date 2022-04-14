<?php
/**
 * Options.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Ui\Component\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;

class Options implements OptionSourceInterface
{
    protected $options = [];

    public function toOptionArray(): array
    {
        $this->options = [
            [
                "value" => StatusInterface::STATUS_PENDING_ACCEPTANCE,
                "label" => __("Pending Acceptance")
            ],
            [
                "value" => StatusInterface::STATUS_ACCEPTED,
                "label" => __("Accepted")
            ],
            [
                "value" => StatusInterface::STATUS_REJECTED,
                "label" => __("Rejected")
            ],
            [
                "value" => StatusInterface::STATUS_PICKING,
                "label" => __("Picking")
            ],
            [
                "value" => StatusInterface::STATUS_READY_TO_BE_SHIPPED,
                "label" => __("Ready to be shipped")
            ],
            [
                "value" => StatusInterface::STATUS_COMPLETED,
                "label" => __("Completed")
            ],
            [
                "value" => StatusInterface::STATUS_ON_THE_WAY,
                "label" => __("On the way")
            ],  [
                "value" => StatusInterface::STATUS_SHIPPED,
                "label" => __("Shipped")
            ],

        ];

        return $this->options;
    }


}
