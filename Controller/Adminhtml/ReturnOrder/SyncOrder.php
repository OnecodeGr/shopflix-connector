<?php
/**
 * SyncOrder.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;
use Onecode\ShopFlixConnector\Helper\UpdateReturnOrder;

class SyncOrder extends ReturnOrder implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::sync_return_order';

    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $syncOrderHelper = $this->_objectManager->create(UpdateReturnOrder::class);
            $syncOrderHelper->update($order);
            $this->messageManager->addSuccessMessage(__("We have successfully updated the order from SHOPFLIX"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->logger->critical($e);
        }
        return $resultRedirect->setPath('shopflix/returnOrder/view', ["order_id" => $order->getId()]);


    }
}
