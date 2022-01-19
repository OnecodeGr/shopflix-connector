<?php
/**
 * History.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\Order;

class History extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
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
        $this->adminHelper = $adminHelper;
        parent::__construct($context, $data);

    }

    public function getStatuses()
    {
        $state = $this->getOrder()->getState();

        $status= $this->getOrder()->getStatus();

        return $this->getOrder()->getConfig()->getStateStatuses($state, $status);
    }

    /**
     * Retrieve order model
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('onecode_shopflix_order');
    }

    /**
     * Check allow to add comment
     *
     * @return bool
     */
    public function canAddComment()
    {
        return $this->_authorization->isAllowed('Onecode_ShopFlixConnector::comment');

    }

    /**
     * Replace links in string
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->adminHelper->escapeHtmlWithLinks($data, $allowedTags);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order_history_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData(
            ['label' => __('Submit Comment'), 'class' => 'action-save action-secondary', 'onclick' => $onclick]
        );
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('shopflix/*/addComment', ['order_id' => $this->getOrder()->getId()]);
    }
}
