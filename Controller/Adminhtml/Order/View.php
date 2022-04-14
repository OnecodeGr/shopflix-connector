<?php
/**
 * View.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

class View extends Order implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::actions_view';

    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order) {
            try {
                $resultPage = $this->_initAction();
                $resultPage->getConfig()->getTitle()->prepend(__('Order'));
            } catch (Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Exception occurred during order load'));
                $resultRedirect->setPath('shopflix/order/index');
                return $resultRedirect;
            }
            $resultPage->getConfig()->getTitle()->prepend(sprintf("#%s", $order->getIncrementId()));
            return $resultPage;
        }
        $resultRedirect->setPath('shopflix/*/');
        return $resultRedirect;
    }
}
