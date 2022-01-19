<?php
/**
 * Items.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Shipment\View;

use Onecode\ShopFlixConnector\Block\Adminhtml\Items\AbstractItems;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\Shipment;

class Items extends AbstractItems
{
    /**
     * Retrieve invoice order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shopflix_shipment');
    }

    /**
     * Retrieve source
     *
     * @return Shipment
     */
    public function getSource()
    {
        return $this->getShipment();
    }
}
