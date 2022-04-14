<?php
/**
 * Sync.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;
use Onecode\ShopFlixConnector\Helper\ExportOrders;
use Onecode\ShopFlixConnector\Helper\ImportOrders;


class Sync extends Order implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::actions_sync_orders';

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $syncOrdersFromShopFlix = $this->_objectManager->create(ImportOrders::class);
            $syncOrdersFromShopFlix->import();
            $syncOrdersToShopFlix = $this->_objectManager->create(ExportOrders::class);
            $syncOrdersToShopFlix->export();
            $this->messageManager->addSuccessMessage(__("We have successfully synced the orders from SHOPFLIX"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->logger->critical($e);
        }
        return $resultRedirect->setPath('shopflix/*/');
    }
}
