<?php
/**
 * ReturnOrderStatusInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

interface ReturnOrderStatusInterface
{
    const STATUS_RETURN_REQUESTED = "return_requested";
    const STATUS_ON_THE_WAY_TO_THE_STORE = "on_the_way_to_the_store";
    const STATUS_DELIVERED_TO_THE_STORE = "delivered_to_the_store";
    const STATUS_RETURN_APPROVED = "approved";
    const STATUS_RETURN_DECLINED = "declined";
}
