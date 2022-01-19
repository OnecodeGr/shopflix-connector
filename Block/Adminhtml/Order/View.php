<?php
/**
 * View.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order;

use DateTime;
use Exception;
use IntlDateFormatter;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Model\Order;

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
            'Order # %1 %2 | %3',
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
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('onecode_shopflix_order');
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
        $this->_controller = 'adminhtml_order';
        $this->_mode = 'view';

        parent::_construct();


        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->removeButton('save');
        $this->setId('shopflix_order_view');
        $order = $this->getOrder();

        if (!$order) {
            return;
        }

        if ($this->_isAllowedAction('Onecode_ShopFlixConnector::actions_edit') && $order->canAccept()) {

            $this->addButton(
                'order_acceptance',
                [
                    'label' => __('Accept'),
                    'class' => 'edit primary',
                    'id' => 'order-view-accept-button',
                    'data_attribute' => [
                        'url' => $this->getAcceptanceUrl()
                    ]
                ]
            );
        }

        if ($this->_isAllowedAction('Onecode_ShopFlix::reject') && $order->canReject()) {
            $this->addButton(
                'order_reject',
                [
                    'label' => __('Reject'),
                    'class' => 'reject',
                    'id' => 'order-view-reject-button',
                    'data_attribute' => [
                        'url' => $this->getRejectUrl()
                    ]
                ]
            );
        }
    }


    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getAcceptanceUrl()
    {
        return $this->getUrl('shopflix/*/accept');
    }

    public function getRejectUrl()
    {
        return $this->getUrl("shopflix/order_reject/start");
    }


    public function getReadyToBeShippedUrl()
    {
        return $this->getUrl("shopflix/*/readyToBeShipped");
    }

    protected function getAcceptanceMessage($order)
    {
        return __('Are you sure? You are going to accept the shopflix Order .');
    }
}
