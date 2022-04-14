<?php
/**
 * Shipments.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

class Shipments extends Order
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->_initOrder();
        dd(1);
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
