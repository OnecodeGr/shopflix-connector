<?php
/**
 * Info.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\Order\AbstractOrder;
use Onecode\ShopFlixConnector\Model\Order;

class Info extends AbstractOrder implements TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_shopflix_order');
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return [
            'can_display_total_paid' => true,
        ];
    }

    public function getOrderInfoData()
    {
        return ['no_use_order_link' => true];
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('order_tracking');
    }

    /**
     * View URL getter
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('shopflix/*/*', ['order_id' => $orderId]);
    }


    public function getItemsHtml()
    {
        return $this->getChildHtml('shopflix_order_items');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
