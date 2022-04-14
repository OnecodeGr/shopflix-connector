<?php
/**
 * Messages.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\View;

use Magento\Framework\Message\CollectionFactory;
use Magento\Framework\Message\Factory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\View\Element\Template\Context;
use Onecode\ShopFlixConnector\Model\ReturnOrder as Order;

class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param Factory $messageFactory
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     * @param InterpretationStrategyInterface $interpretationStrategy
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context                         $context,
        Factory                         $messageFactory,
        CollectionFactory               $collectionFactory,
        ManagerInterface                $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        Registry                        $registry,
        array                           $data = []
    )
    {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
        $this->coreRegistry = $registry;
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /**
         * Check Item products existing
         */
        $productIds = [];
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve order model instance
     *
     * @return Order
     */
    protected function _getOrder(): Order
    {
        return $this->coreRegistry->registry('onecode_shopflix_return_order');
    }
}
