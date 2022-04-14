<?php
/**
 * GridAsyncInsertObserver.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterfaceAlias;
use Onecode\ShopFlixConnector\Model\GridAsyncInsert;

class GridAsyncInsertObserver implements ObserverInterfaceAlias
{


    /**
     * @var GridAsyncInsert
     */
    private $asyncInsert;

    public function __construct(GridAsyncInsert $asyncInsert){
        $this->asyncInsert = $asyncInsert;
    }
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        return $this->asyncInsert->asyncInsert();
    }
}
