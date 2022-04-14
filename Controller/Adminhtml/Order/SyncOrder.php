<?php
/**
 * SyncOrder.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;
use Onecode\ShopFlixConnector\Helper\UpdateOrder;

class SyncOrder extends Order implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::sync_order';

    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $syncOrderHelper = $this->_objectManager->create(UpdateOrder::class);
            $syncOrderHelper->update($order);
            $this->messageManager->addSuccessMessage(__("We have successfully updated the order from SHOPFLIX"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->logger->critical($e);
        }
        return $resultRedirect->setPath('shopflix/order/view', ["order_id" => $order->getId()]);


    }
}
