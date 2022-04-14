<?php
/**
 * Config.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder;

use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusInterface;

class Config
{


    /**
     *
     * @param string $state
     * @param  $orderStatus
     * @return  array
     */
    public function getStateStatuses(string $state, $orderStatus): array
    {
        $status = [
            ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX => [
            ],
            ReturnOrderInterface::STATE_DELIVERED_TO_THE_STORE => [
                ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE => __("Delivered to the store"),
            ],
            ReturnOrderInterface::STATE_APPROVED => [
                ReturnOrderStatusInterface::STATUS_RETURN_APPROVED => __("Approved"),
            ],
            ReturnOrderInterface::STATE_DECLINED => [
                ReturnOrderStatusInterface::STATUS_RETURN_DECLINED => __("Declined"),
            ],
        ];

        if ($orderStatus !== false && $state == OrderInterface::STATE_ACCEPTED) {
            switch ($orderStatus) {
                case ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED:
                    $status[$state][$orderStatus] = __("Returned Requested");
                    break;
                case ReturnOrderStatusInterface::STATUS_ON_THE_WAY_TO_THE_STORE:
                    $status[$state][$orderStatus] = __("On the way of the store");
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
            ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX => ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED,
            ReturnOrderInterface::STATE_DELIVERED_TO_THE_STORE => ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE,
            ReturnOrderInterface::STATE_APPROVED => ReturnOrderStatusInterface::STATUS_RETURN_APPROVED,
            ReturnOrderInterface::STATE_DECLINED => ReturnOrderStatusInterface::STATUS_RETURN_DECLINED,
        ];

        return $status[$state] ?? "";
    }

}
