<?php
/**
 * StatusInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

interface StatusInterface
{
    const STATUS_PICKING = "picking";
    const STATUS_ACCEPTED = "accepted";
    const STATUS_READY_TO_BE_SHIPPED = "ready_to_be_shipped";
    const STATUS_PENDING_ACCEPTANCE = "pending_acceptance";
    const STATUS_REJECTED = "rejected";
    const STATUS_ON_THE_WAY = "on_the_way";
    const STATUS_COMPLETED = "completed";
    const STATUS_SHIPPED = "shipped";
    const STATUS_CANCELED = "canceled";
    const STATUS_PARTIAL_SHIPPED = "partial_shipped";
}
