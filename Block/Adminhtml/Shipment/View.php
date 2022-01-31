<?php
/**
 * View.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Shipment;

use DateTime;
use IntlDateFormatter;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Shipment;

class View extends Container
{
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
        array    $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Phrase
     */
    public function getHeaderText()
    {

        return __(
            'Shipment #%1 | %3',
            $this->getShipment()->getIncrementId(),
            $this->formatDate(
                $this->_localeDate->date(new DateTime($this->getShipment()->getCreatedAt())),
                IntlDateFormatter::MEDIUM,
                true
            )
        );
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shopflix_shipment');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'shopflix/order/view',
            [
                'order_id' => $this->getShipment() ? $this->getShipment()->getOrderId() : null,
                'active_tab' => 'order_shipments'
            ]
        );
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getShipment()->getBackUrl()) {
                return $this->buttonList->update(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getShipment()->getBackUrl() . '\')'
                );
            }
            return $this->buttonList->update(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl('marketpalce/shipment/') . '\')'
            );
        }
        return $this;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'shipment_id';
        $this->_mode = 'view';

        parent::_construct();

        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('save');
        if (!$this->getShipment()) {
            return;
        }

        if ($this->getShipment()->getId()) {
            $this->buttonList->add(
                'print',
                [
                    'label' => __('Print'),
                    'class' => 'save',
                    'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('shopflix/shipment/print', ['shipment_id' => $this->getShipment()->getId()]);
    }
}
