<?php
/**
 * SyncOrdersToShopFlix.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Cron;

use Onecode\ShopFlixConnector\Helper\ExportOrders;

class SyncOrdersToShopFlix
{


    private $exportOrders;


    public function __construct(ExportOrders $exportOrders)
    {
        $this->exportOrders = $exportOrders;
    }

    public function execute()
    {
        $this->exportOrders->export();
    }
}
