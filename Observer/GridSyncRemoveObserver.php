<?php
/**
 * GridSyncRemoveObserver.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Observer;

use Magento\Framework\Event\Observer;
use Onecode\ShopFlixConnector\Model\ResourceModel\GridInterface;

class GridSyncRemoveObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Entity grid model.
     *
     * @var GridInterface
     */
    protected $entityGrid;

    /**
     * @param GridInterface $entityGrid
     */
    public function __construct(
        GridInterface $entityGrid
    ) {
        $this->entityGrid = $entityGrid;
    }
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $this->entityGrid->purge($observer->getDataObject()->getId());
    }
}
