<?php
/**
 * View.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */
declare(strict_types=1);

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Reject\AbstractReject;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Order;

abstract class View extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::reject';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var mixed|OrderRepositoryInterface
     */
    private $orderRepository;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param ForwardFactory $resultForwardFactory
     * @param OrderRepositoryInterface|null $orderRepository
     */
    public function __construct(
        Context                  $context,
        Registry                 $registry,
        ForwardFactory           $resultForwardFactory,
        OrderRepositoryInterface $orderRepository = null
    )
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->orderRepository = $orderRepository ?:
            ObjectManager::getInstance()->get(OrderRepositoryInterface::class);
    }

    /**
     * Invoice information page
     *
     * @return Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        if ($this->getRequest()->getParam('order_id')) {
            $resultForward->setController('order_reject')
                ->setParams(['come_from' => 'reject'])
                ->forward('view');
        } else {
            $resultForward->forward('noroute');
        }
        return $resultForward;
    }

    /**
     * Get order using order Id from request params
     *
     * @return Order|bool
     */
    protected function getOrder()
    {
        try {
            $order = $this->orderRepository->getById($this->getRequest()->getParam('order_id'));
            $this->registry->register('shopflix_current_order', $order);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Order loading error'));
            return false;
        }

        return $order;
    }
}
