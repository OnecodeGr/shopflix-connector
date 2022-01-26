<?php
/**
 * Shopflix.php
 *
 * @copyright Copyright © 2022 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Sales\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Model\OrderRepository;

class Shopflix extends Template
{

    private $_coreRegistry;
    /**
     * @var OrderRepository
     */
    private $shopFlixRepository;

    public function __construct(
        Context          $context,
        Registry         $registry,
        OrderRepository  $shopFlixOrderRepository,
        array            $data = [],
        ?JsonHelper      $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null)
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->_coreRegistry = $registry;
        $this->shopFlixRepository = $shopFlixOrderRepository;
    }

    public function getShopFlixOrder()
    {

        $magentoOrder = $this->getOrder();
        if ($magentoOrder->getData("is_shopflix")) {
            try {
                return $this->shopFlixRepository->getByMagentoOrderId($magentoOrder->getId());
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;

    }

    public function getOrder()
    {
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new LocalizedException(__('We can\'t get the order instance right now.'));
    }
}
