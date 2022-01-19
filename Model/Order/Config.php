<?php
/**
 * Config.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;

class Config
{


    /**
     *
     * @param string $state
     * @param bool $orderStatus
     * @return  array
     */
    public function getStateStatuses(string $state, bool $orderStatus): array
    {
        $status = [
            OrderInterface::STATE_ACCEPTED => [],
            OrderInterface::STATE_PENDING_ACCEPTANCE => [
                StatusInterface::STATUS_PENDING_ACCEPTANCE => __("Pending Acceptance")
            ],
            OrderInterface::STATE_REJECTED => [
                StatusInterface::STATUS_REJECTED => __("Rejected")
            ],
            OrderInterface::STATE_COMPLETED => [
                StatusInterface::STATUS_ON_THE_WAY => __("On the way"),
                StatusInterface::STATUS_PARTIAL_SHIPPED=> __("Partial Shipped"),
                StatusInterface::STATUS_SHIPPED => __("Shipped"),
                StatusInterface::STATUS_COMPLETED => __("Completed"),
                StatusInterface::STATUS_READY_TO_BE_SHIPPED => __("Ready to be shipped")
            ],
            OrderInterface::STATE_CANCELED => [
                StatusInterface::STATUS_CANCELED => __("Canceled"),
            ],
        ];


        if ($orderStatus !== false && $state == OrderInterface::STATE_ACCEPTED) {
            switch ($orderStatus) {
                case StatusInterface::STATUS_ACCEPTED:
                    $status[$state][$orderStatus] = __("Accepted");
                    break;
                case StatusInterface::STATUS_PICKING:
                    $status[$state][$orderStatus] = __("Picking");
                    break;
            }
        }
        return $status[$state] ?? [];
    }

    /**
     * @param string $state
     * @return string
     */
    public function getStateDefaultStatus(string $state): string
    {
        $status = [
            OrderInterface::STATE_ACCEPTED => StatusInterface::STATUS_PICKING,
            OrderInterface::STATE_COMPLETED => StatusInterface::STATUS_ON_THE_WAY,
            OrderInterface::STATE_PENDING_ACCEPTANCE => StatusInterface::STATUS_PENDING_ACCEPTANCE,
            OrderInterface::STATE_REJECTED => StatusInterface::STATUS_REJECTED,
            OrderInterface::STATE_CANCELED => StatusInterface::STATUS_CANCELED
        ];

        return $status[$state] ?? "";
    }

}
