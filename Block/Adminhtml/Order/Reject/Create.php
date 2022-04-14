<?php
/**
 * Create.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\Reject;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;

class Create extends Container
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
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText(): string
    {
        return __(
            'Rejection for Order #%1',
            $this->getOrder()->getIncrementId()
        );
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        return $this->_coreRegistry->registry('current_shopflix_order');
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl(
            'shopflix/order/view',
            ['order_id' => $this->getOrder() ? $this->getOrder()->getId() : null]
        );
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order_reject';
        $this->_mode = 'create';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('delete');
    }
}
