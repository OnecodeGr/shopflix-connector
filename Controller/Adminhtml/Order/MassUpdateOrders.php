<?php
/**
 * MassUpdateOrders.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Helper\UpdateOrder;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\CollectionFactory;

class MassUpdateOrders extends AbstractMassAction implements HttpPostActionInterface
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::mass_action_update_orders';

    private $updateOrder;

    public function __construct(Context           $context,
                                Filter            $filter,
                                CollectionFactory $collectionFactory,
                                UpdateOrder       $updateOrder)
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->updateOrder = $updateOrder;
    }

    /**
     * @inheritDoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var OrderInterface $order */
        $items = 0;
        foreach ($collection->getItems() as $order) {
            try {
                $this->updateOrder->update($order);
                if ($items++ === 0) {
                    $this->messageManager->addSuccessMessage(__("We have successfully updated the orders from SHOPFLIX"));
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }

        return $resultRedirect->setPath("*/*");
    }
}
