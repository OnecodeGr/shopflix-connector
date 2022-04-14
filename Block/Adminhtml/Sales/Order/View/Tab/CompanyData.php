<?php
/**
 * CompanyData.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Sales\Order\View\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Model\OrderRepository;

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
     * @var OrderRepository
     */
    private $shopFlixRepository;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context         $context,
        Registry        $registry,
        OrderRepository $shopFlixOrderRepository,
        array           $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->shopFlixRepository = $shopFlixOrderRepository;

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
        return $this->getOrder() && $this->getOrder()->isInvoice();
    }

    public function getOrder()
    {

        $magentoOrder = $this->getMagentoOrder();
        if ($magentoOrder->getData("is_shopflix")) {
            try {
                return $this->shopFlixRepository->getByMagentoOrderId($magentoOrder->getId());
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;

    }

    public function getMagentoOrder()
    {
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new LocalizedException(__('We can\'t get the order instance right now.'));
    }

    public function isHidden()
    {
        return $this->getOrder() && !$this->getOrder()->isInvoice();
    }
}
