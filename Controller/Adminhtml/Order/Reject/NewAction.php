<?php
/**
 * Reject.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order\Reject;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Order;


class NewAction extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::reject';
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        Context                  $context,
        PageFactory              $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        Registry                 $registry
    )
    {
        $this->orderRepository = $orderRepository;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Reject create page
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        try {
            /** @var Order $order */
            $order = $this->orderRepository->getById($orderId);

            if (!$order->canReject()) {
                throw new LocalizedException(
                    __('The order can not rejected')
                );
            }
            $this->registry->register('current_shopflix_order', $order);
            $this->registry->register('onecode_shopflix_order', $order);
            $comment = $this->_objectManager->get(Session::class)->getCommentText(true);

            /** @var Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Onecode_ShopFlixConnector::shopflix_order');
            $resultPage->getConfig()->getTitle()->prepend(__('Reject'));
            $resultPage->getConfig()->getTitle()->prepend(__('Reject Order'));
            return $resultPage;
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $this->_redirectToOrder($orderId);
        } catch (Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, 'Cannot create an invoice.');
            return $this->_redirectToOrder($orderId);
        }
    }

    /**
     * Redirect to order view page
     *
     * @param int $orderId
     * @return Redirect
     */
    protected function _redirectToOrder($orderId)
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('shopflix/order/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }
}
