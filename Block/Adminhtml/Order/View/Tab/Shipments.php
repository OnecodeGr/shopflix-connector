<?php
/**
 * Shipments.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Text\ListText;
use Onecode\ShopFlixConnector\Model\Order;

class Shipments extends ListText implements TabInterface
{


    /**
     * @var Registry
     */
    private $_coreRegistry;


    public function __construct(Context $context, Registry $coreRegistry, array $data = [])
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return Order
     */
    public function getOrder(): Order
    {

        return $this->_coreRegistry->registry('current_shopflix_order');

    }

    public function getTabLabel()
    {
        return __('Shipments');
    }

    public function getTabTitle()
    {
        return __('SHOPFLIX Order Shipments');
    }

    public function canShowTab(): bool
    {
        return true;
    }

    public function isHidden(): bool
    {
        return false;
    }
}
