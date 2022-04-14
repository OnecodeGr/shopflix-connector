<?php
/**
 * Form.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Shipment\View;

use Magento\Backend\Block\Widget\Button;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\Order\AbstractOrder;
use Onecode\ShopFlixConnector\Model\Order\Shipment;

class Form extends AbstractOrder
{
    /**
     * Retrieve order
     *
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    public function getShipment(): Shipment
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


    /**
     * Get print label button html
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPrintLabelButton(): string
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('adminhtml/shipment/printLabel', $data);
        return $this->getLayout()->createBlock(
            Button::class
        )->setData(
            ['label' => __('Print Shipping Label'), 'onclick' => 'setLocation(\'' . $url . '\')']
        )->toHtml();
    }
}
