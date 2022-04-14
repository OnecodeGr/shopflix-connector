<?php
/**
 * View.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder;

use DateTime;
use Exception;
use IntlDateFormatter;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Model\ReturnOrder as Order;

class View extends Container
{
    /**
     * Block group
     *
     * @var string
     */
    protected $_blockGroup = 'Onecode_ShopFlixConnector';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context  $context,
        Registry $registry,
                 $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @throws Exception
     */
    public function getHeaderText()
    {
        $_extOrderId = $this->getOrder()->getShopFlixOrderId();
        if ($_extOrderId) {
            $_extOrderId = '[' . $_extOrderId . '] ';
        } else {
            $_extOrderId = '';
        }
        return __(
            'Return Order # %1 %2 | %3',
            $this->getOrder()->getIncrementId(),
            $_extOrderId,
            $this->formatDate(
                $this->_localeDate->date(new DateTime($this->getOrder()->getCreatedAt())),
                IntlDateFormatter::MEDIUM,
                true
            )
        );
    }

    /**
     * @return Order | null
     */
    public function getOrder(): ?Order
    {
        return $this->_coreRegistry->registry('current_shopflix_return_order');
    }

    public function getBackUrl()
    {
        return $this->getUrl('shopflix/*/');
    }

    public function getUrl($params = '', $params2 = [])
    {
        $params2['order_id'] = $this->getOrderId();
        return parent::getUrl($params, $params2);
    }

    public function getOrderId()
    {
        return $this->getOrder() ? $this->getOrder()->getId() : null;
    }

    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_returnOrder';
        $this->_mode = 'view';

        parent::_construct();


        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->removeButton('save');
        $this->setId('shopflix_returnorder_view');
        $order = $this->getOrder();

        if (!$order) {
            return;
        }

        if ($this->_isAllowedAction('Onecode_ShopFlixConnector::return_order_accept') && $order->canApprove()) {

            $this->addButton(
                'order_acceptance',
                [
                    'label' => __('Accept'),
                    'class' => 'edit primary shopflix-icon-checkmark',
                    'id' => 'return-order-view-accept-button',
                    'data_attribute' => [
                        'url' => $this->getAcceptanceUrl()
                    ]
                ]
            );
        }

        if ($this->_isAllowedAction('Onecode_ShopFlixConnector::return_order_decline') && $order->canDecline()) {
            $this->addButton(
                'order_reject',
                [
                    'label' => __('Reject'),
                    'class' => 'reject',
                    'id' => 'return-order-view-reject-button',
                    'data_attribute' => [
                        'url' => $this->getRejectUrl()
                    ]
                ]
            );
        }

        if($this->_isAllowedAction('Onecode_ShopFlixConnector::sync_order')){
            $this->addButton(
                'sync_order',
                [
                    'label' => __('Update Order'),
                    'class' => 'sync primary-blue shopflix-icon-loop2',
                    'id' => 'return-order-view-sync-button',
                    'data_attribute' => [
                        'url' => $this->getSyncOrdersUrl()
                    ]
                ],
            );
        }
    }


    protected function _isAllowedAction($resourceId): bool
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getAcceptanceUrl(): string
    {
        return $this->getUrl('shopflix/*/accept');
    }

    public function getRejectUrl(): string
    {
        return $this->getUrl("shopflix/*/reject");
    }

    public function getSyncOrdersUrl(): string
    {
        return $this->getUrl("shopflix/*/syncOrder");
    }

    protected function getAcceptanceMessage($order): \Magento\Framework\Phrase
    {
        return __('Are you sure? You are going to accept the shopflix Order .');
    }
}
