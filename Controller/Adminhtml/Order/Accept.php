<?php
/**
 * Accept.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;


use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

class Accept extends Order implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::accept';

    /**
     * Cancel order
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->isValidPostRequest()) {
            $this->messageManager->addErrorMessage(__('You have not accept the item.'));
            return $resultRedirect->setPath('shopflix/*/');
        }
        $order = $this->_initOrder();
        if ($order) {
            try {
                $this->orderManagement->accept($order->getEntityId());
                $this->messageManager->addSuccessMessage(__('You accepted the order.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not accepted the item.'));
                $this->logger->critical($e);
            }
            return $resultRedirect->setPath('shopflix/order/view', ['order_id' => $order->getId()]);
        }
        return $resultRedirect->setPath('shopflix/*/');
    }
}
