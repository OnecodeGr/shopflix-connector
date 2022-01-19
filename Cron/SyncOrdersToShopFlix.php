<?php
/**
 * SyncOrdersToShopFlix.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Cron;

use Onecode\ShopFlixConnector\Helper\ExportOrders;

class SyncOrdersToShopFlix
{

    /**
     * @var ExportOrders
     */
    private $exportOrders;

    /**
     * @param ExportOrders $exportOrders
     */
    public function __construct(ExportOrders $exportOrders)
    {
        $this->exportOrders = $exportOrders;
    }

    public function execute()
    {
        $this->exportOrders->export();
    }
}
