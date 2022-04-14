<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 ${ORGANIZATION_NAME}  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */


namespace Onecode\ShopFlixConnector\Cron;


use Onecode\ShopFlixConnector\Helper\ExportReturnOrders;

class SyncReturnOrdersToShopFlix
{


    /**
     * @var ExportReturnOrders
     */
    private $exportOrders;

    public function __construct(ExportReturnOrders $exportReturnOrders)
    {
        $this->exportOrders = $exportReturnOrders;

    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $this->exportOrders->export();
    }
}
