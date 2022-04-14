<?php
/**
 * Info.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\AbstractOrder;

class Info extends AbstractOrder implements TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return ReturnOrderInterface
     */
    public function getSource(): ReturnOrderInterface
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order model instance
     *
     * @return ReturnOrderInterface
     */
    public function getOrder(): ReturnOrderInterface

    {
        return $this->_coreRegistry->registry('current_shopflix_return_order');
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData(): array
    {
        return [
            'can_display_total_paid' => true,
        ];
    }

    public function getOrderInfoData(): array
    {
        return ['no_use_order_link' => true];
    }


    /**
     * View URL getter
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl(int $orderId): string
    {
        return $this->getUrl('shopflix/*/*', ['order_id' => $orderId]);
    }


    public function getItemsHtml(): string
    {
        return $this->getChildHtml('shopflix_return_order_items');
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
        return __('Return Order Information');
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
