<?php
/**
 * Order.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Adminhtml shopflix orders controller
 *
 * @author       Spyros Bodinis {spyros@onecode.gr}
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Order extends Action
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::shopflix_order';

    protected $_coreRegistry = null;
    protected $_fileFactory;
    protected $_translateInline;
    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $resultLayoutFactory;
    protected $resultRawFactory;
    protected $orderRepository;
    protected $logger;
    protected $orderManagement;

    /**
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param InlineInterface $translateInline
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ManagementInterface $orderManagement
     * @param LoggerInterface $logger
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context           $context,
        Registry                 $coreRegistry,
        FileFactory              $fileFactory,
        InlineInterface          $translateInline,
        PageFactory              $resultPageFactory,
        JsonFactory              $resultJsonFactory,
        LayoutFactory            $resultLayoutFactory,
        RawFactory               $resultRawFactory,
        OrderRepositoryInterface $orderRepository,
        ManagementInterface      $orderManagement,
        LoggerInterface          $logger
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->logger = $logger;
        parent::__construct($context);
        $this->_publicActions = ['view', 'index'];
    }


    /**
     * Init layout, menu and breadcrumb
     *
     * @return Page
     */
    protected function _initAction(): Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Onecode_ShopFlixConnector::listing');
        $resultPage->addBreadcrumb(__('ShopFlix'), __('ShopFlix'));
        $resultPage->addBreadcrumb(__('Order'), __('Order'));
        return $resultPage;
    }

    /**
     * Initialize order model instance
     *
     * @return OrderInterface|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->orderRepository->getById($id);
        } catch (NoSuchEntityException | InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('onecode_shopflix_order', $order);
        $this->_coreRegistry->register('current_shopflix_order', $order);
        return $order;
    }

    /**
     * @return bool
     */
    protected function isValidPostRequest(): bool
    {
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        return ($formKeyIsValid && $isPost);
    }
}
