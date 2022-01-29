<?php
/**
 * Sync.php
 *
 * @copyright Copyright © 2022 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;
use Onecode\ShopFlixConnector\Helper\ImportShipments;


class Sync extends Order implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::actions_sync_shipments';

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $syncShipmentsFromShopFlix = $this->_objectManager->create(ImportShipments::class);
            $syncShipmentsFromShopFlix->import();
            $this->messageManager->addSuccessMessage(__("We have successfully synced the shipments from SHOPFLIX"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->logger->critical($e);
        }
        return $resultRedirect->setPath('shopflix/*/');
    }

}
