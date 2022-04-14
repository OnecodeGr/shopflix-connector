<?php
/**
 * SyncOrdersFromShopFlix.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Cron;

use Onecode\ShopFlixConnector\Helper\ImportOrders;

class SyncOrdersFromShopFlix
{


    private $importOrders;

    public function __construct(ImportOrders $importOrders)
    {
        $this->importOrders = $importOrders;
    }


    public function execute()
    {
        $this->importOrders->import();
    }
}
