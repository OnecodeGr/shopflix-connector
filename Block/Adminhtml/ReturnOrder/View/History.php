<?php
/**
 * History.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Helper\Admin;

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

    public function getStatuses(): array
    {
        $state = $this->getOrder()->getState();

        $status= $this->getOrder()->getStatus();

        return $this->getOrder()->getConfig()->getStateStatuses($state, $status);
    }

    /**
     * Retrieve order model
     *
     * @return ReturnOrderInterface
     */
    public function getOrder(): ReturnOrderInterface
    {
        return $this->_coreRegistry->registry('onecode_shopflix_return_order');
    }

    /**
     * Check allow to add comment
     *
     * @return bool
     */
    public function canAddComment(): bool
    {
        return $this->_authorization->isAllowed('Onecode_ShopFlixConnector::return_order_comment');

    }

    /**
     * Replace links in string
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null): string
    {
        return $this->adminHelper->escapeHtmlWithLinks($data, $allowedTags);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout(): History
    {
        $onclick = "submitAndReloadArea($('return_order_history_block').parentNode, '" . $this->getSubmitUrl() . "')";
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
    public function getSubmitUrl(): string
    {
        return $this->getUrl('shopflix/*/addComment', ['order_id' => $this->getOrder()->getId()]);
    }


}
