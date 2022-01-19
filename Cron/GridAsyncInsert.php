<?php
/**
 * GridAsyncInsert.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Cron;

class GridAsyncInsert
{
    /**
     **
     * @var \Onecode\ShopFlixConnector\Model\GridAsyncInsert
     */
    protected $asyncInsert;

    /**
     * @param \Onecode\ShopFlixConnector\Model\GridAsyncInsert $asyncInsert
     */
    public function __construct(
        \Onecode\ShopFlixConnector\Model\GridAsyncInsert $asyncInsert
    )
    {
        $this->asyncInsert = $asyncInsert;
    }

    /**
     * Handles asynchronous insertion of the new entity into
     * corresponding grid during cron job.
     *
     * Also method is used in the next events:
     *
     * - config_data_dev_grid_async_indexing_disabled
     *
     * Works only if asynchronous grid indexing is enabled
     * in global settings.
     *
     * @return void
     */
    public function execute()
    {
        $this->asyncInsert->asyncInsert();
    }
}
