<?php
/**
 * Accept.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;


use Exception;
use GuzzleHttp\Exception\RequestException;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;

class Accept extends ReturnOrder implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::return_order_accept';

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
                $this->orderManagement->approved($order->getEntityId());
                $this->messageManager->addSuccessMessage(__('You accepted the order.'));
            } catch (RequestException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not accepted the item.'));
                $this->logger->critical($e);
            }
            return $resultRedirect->setPath('shopflix/returnOrder/view', ['order_id' => $order->getId()]);
        }
        return $resultRedirect->setPath('shopflix/*/');
    }
}
