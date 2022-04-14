<?php
/**
 * Sync.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;
use Onecode\ShopFlixConnector\Helper\ImportReturnOrders;


class Sync extends ReturnOrder implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::return_order_actions_sync_orders';

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $syncOrdersFromShopFlix = $this->_objectManager->create(ImportReturnOrders::class);
            $syncOrdersFromShopFlix->import();
            #$syncOrdersToShopFlix = $this->_objectManager->create(ExportOrders::class);
            #$syncOrdersToShopFlix->export();
            $this->messageManager->addSuccessMessage(__("We have successfully synced the returned orders from SHOPFLIX"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->logger->critical($e);
        }
        return $resultRedirect->setPath('shopflix/*/');
    }
}
