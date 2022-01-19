<?php
/**
 * SyncOrdersShipment.php
 *
 * @copyright Copyright © 2022 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Cron;

use Onecode\ShopFlixConnector\Helper\ImportShipments;

class SyncOrdersShipment
{
    /**
     * @var ImportShipments
     */
    private $importShipments;

    /**
     * @param ImportShipments $importShipments
     */
    public function __construct(ImportShipments $importShipments)
    {
        $this->importShipments = $importShipments;
    }

    public function execute()
    {
        $this->importShipments->import();
    }
}
