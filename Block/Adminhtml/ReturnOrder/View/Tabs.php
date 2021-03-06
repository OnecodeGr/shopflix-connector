<?php
/**
 * Tabs.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Model\ReturnOrder as Order;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context          $context,
        EncoderInterface $jsonEncoder,
        Session          $authSession,
        Registry         $registry,
        array            $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Retrieve available order
     *
     * @return Order
     * @throws LocalizedException
     */
    public function getOrder(): Order
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if ($this->_coreRegistry->registry('current_shopflix_return_order')) {
            return $this->_coreRegistry->registry('current_shopflix_return_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new LocalizedException(__('We can\'t get the order instance right now.'));
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('shopflix_returnorder_view_tabs');
        $this->setDestElementId('shopflix_returnorder_view');
        $this->setTitle(__('Return Order View'));
    }
}
