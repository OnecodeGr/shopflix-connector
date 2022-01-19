<?php
/**
 * AbstractMassAction.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;

abstract class AbstractMassAction extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::actions';

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/';


    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var object
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(Context $context, Filter $filter)
    {
        parent::__construct($context);
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Set status to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction(AbstractCollection $collection);

    /**
     * @return string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'shopflix/shipment/';
    }
}
