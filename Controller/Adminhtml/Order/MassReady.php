<?php
/**
 * MassReady.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\CollectionFactory;

class MassReady extends AbstractMassAction implements HttpPostActionInterface
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::ready_to_be_shipped';


    /**
     * @var ManagementInterface
     */
    private $orderManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ManagementInterface|null $orderManagement
     */
    public function __construct(
        Context                  $context,
        Filter                   $filter,
        CollectionFactory        $collectionFactory,
        ManagementInterface $orderManagement = null
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement ?: ObjectManager::getInstance()->get(
            ManagementInterface::class
        );
    }

    /**
     * Cancel selected orders
     *
     * @param AbstractCollection $collection
     * @return Redirect
     */
    protected function massAction(AbstractCollection $collection): Redirect
    {
        $countCancelOrder = 0;
        /** @var OrderInterface $order */
        foreach ($collection->getItems() as $order) {
            $isCanceled = $this->orderManagement->readyToBeShipped($order->getEntityId());
            if ($isCanceled === false) {
                continue;
            }
            $countCancelOrder++;
        }
        $countNonCancelOrder = $collection->count() - $countCancelOrder;

        if ($countNonCancelOrder && $countCancelOrder) {
            $this->messageManager->addErrorMessage(__('%1 order(s) cannot be ready to be shipped.', $countNonCancelOrder));
        } elseif ($countNonCancelOrder) {
            $this->messageManager->addErrorMessage(__('You cannot ready to be shipped the order(s).'));
        }

        if ($countCancelOrder) {
            $this->messageManager->addSuccessMessage(__('We changed the status to Ready to be shipped %1 order(s).', $countCancelOrder));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }


}
