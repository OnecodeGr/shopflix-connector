<?php
/**
 * Form.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Shipment\View;

use Onecode\ShopFlixConnector\Block\Adminhtml\Order\AbstractOrder;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\Shipment;

class Form extends AbstractOrder
{
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
     * Retrieve order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
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


    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('adminhtml/shipment/printLabel', $data);
        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            ['label' => __('Print Shipping Label'), 'onclick' => 'setLocation(\'' . $url . '\')']
        )->toHtml();
    }
}
