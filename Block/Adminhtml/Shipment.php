<?php

namespace Onecode\ShopFlixConnector\Block\Adminhtml;

class Shipment extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'shopflix_shipment';
        $this->_blockGroup = 'Onecode_ShopFlixConnector';
        $this->_headerText = __('Shipments');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
