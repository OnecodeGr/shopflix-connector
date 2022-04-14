<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 ${ORGANIZATION_NAME}  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */


namespace Onecode\ShopFlixConnector\Cron;



use Onecode\ShopFlixConnector\Helper\ImportReturnOrders;

class SyncReturnOrdersFromShopflix
{

    private $importOrders;

    public function __construct(ImportReturnOrders $importOrders)
    {
        $this->importOrders = $importOrders;
    }
    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $this->importOrders->import();
    }
}
