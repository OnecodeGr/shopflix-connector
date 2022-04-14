<?php
/**
 * GridAsyncInsert.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\GridInterface;

class GridAsyncInsert
{
    /**
     * Entity grid model.
     *
     * @var GridInterface
     */
    protected $entityGrid;

    /**
     * Global configuration storage.
     *
     * @var ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * @param GridInterface $entityGrid
     * @param ScopeConfigInterface $globalConfig
     */
    public function __construct(
        GridInterface        $entityGrid,
        ScopeConfigInterface $globalConfig
    )
    {
        $this->entityGrid = $entityGrid;
        $this->globalConfig = $globalConfig;
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
    public function asyncInsert()
    {
        if ($this->globalConfig->getValue('dev/grid/async_indexing')) {
            $this->entityGrid->refreshBySchedule();
        }
    }
}
