<?php
/**
 * CompanyData.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\Order;

class CompanyData extends Template implements TabInterface
{

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Onecode_ShopFlixConnector::order/view/tab/company_data.phtml';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Admin
     */
    private $adminHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        Context  $context,
        Registry $registry,
        Admin    $adminHelper,
        array    $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->adminHelper = $adminHelper;
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


    public function getTabLabel()
    {
        return __('Company Data');
    }

    public function getTabTitle()
    {
        return __('Company Data');
    }

    public function canShowTab()
    {

        return $this->getOrder()->isInvoice();
    }

    public function isHidden()
    {
        return !$this->getOrder()->isInvoice();
    }

}
