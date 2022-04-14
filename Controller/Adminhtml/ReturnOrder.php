<?php
/**
 * ReturnOrder.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
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
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderManagementInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Adminhtml shopflix return orders controller
 *
 * @author       Spyros Bodinis {spyros@onecode.gr}
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class ReturnOrder extends Action
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::shopflix_return_order';
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
     * @param ReturnOrderRepositoryInterface $orderRepository
     * @param ReturnOrderManagementInterface $orderManagement
     * @param LoggerInterface $logger
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context                 $context,
        Registry                       $coreRegistry,
        FileFactory                    $fileFactory,
        InlineInterface                $translateInline,
        PageFactory                    $resultPageFactory,
        JsonFactory                    $resultJsonFactory,
        LayoutFactory                  $resultLayoutFactory,
        RawFactory                     $resultRawFactory,
        ReturnOrderRepositoryInterface $orderRepository,
        ReturnOrderManagementInterface $orderManagement,
        LoggerInterface                $logger
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
        $resultPage->setActiveMenu('Onecode_ShopFlixConnector::listing_return_order');
        $resultPage->addBreadcrumb(__('ShopFlix'), __('ShopFlix'));
        $resultPage->addBreadcrumb(__('Return Order'), __('Return Order'));
        return $resultPage;
    }

    /**
     * Initialize order model instance
     *
     * @return ReturnOrderInterface|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->orderRepository->getById($id);
        } catch (NoSuchEntityException|InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('onecode_shopflix_return_order', $order);
        $this->_coreRegistry->register('current_shopflix_return_order', $order);
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
