<?php
/**
 * GridSyncInsertObserver.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\GridInterface;


class GridSyncInsertObserver implements ObserverInterface
{

    protected $entityGrid;

    protected $globalConfig;


    /**
     * @param GridInterface $entityGrid
     * @param ScopeConfigInterface $globalConfig
     */
    public function __construct(GridInterface        $entityGrid,
                                ScopeConfigInterface $globalConfig)
    {
        $this->entityGrid = $entityGrid;
        $this->globalConfig = $globalConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->globalConfig->getValue('dev/grid/async_indexing')) {
            $this->entityGrid->refresh($observer->getObject()->getId());

        }
    }
}
