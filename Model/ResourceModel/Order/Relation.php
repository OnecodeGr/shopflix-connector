<?php
/**
 * Relation.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
use Onecode\ShopFlixConnector\Api\ItemRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Handler\Address as AddressHandler;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status\History;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status\History as StatusHistoryResource;

class Relation implements RelationInterface
{
    /**
     * @var AddressHandler
     */
    protected $addressHandler;

    /**
     * @var ItemRepositoryInterface
     */
    protected $orderItemRepository;


    /**
     * @var History
     */
    protected $orderStatusHistoryResource;

    /**
     * @param AddressHandler $addressHandler
     * @param ItemRepositoryInterface $orderItemRepository
     * @param StatusHistoryResource $orderStatusHistoryResource
     */
    public function __construct(
        AddressHandler             $addressHandler,
        ItemRepositoryInterface    $orderItemRepository,
        StatusHistoryResource $orderStatusHistoryResource
    )
    {
        $this->addressHandler = $addressHandler;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderStatusHistoryResource = $orderStatusHistoryResource;
    }

    public function processRelation(AbstractModel $object)
    {
        /** @var Order $object */

        if (null !== $object->getItems()) {
            /** @var \Onecode\ShopFlixConnector\Model\Order\Item $item */
            foreach ($object->getItems() as $item) {
                $item->setOrderId($object->getId());
                $item->setOrder($object);
                $this->orderItemRepository->save($item);
            }
        }

        if (null !== $object->getStatusHistories()) {
            /** @var \Onecode\ShopFlixConnector\Model\Order\Status\History $statusHistory */
            foreach ($object->getStatusHistories() as $statusHistory) {
                $statusHistory->setParentId($object->getId());
                $statusHistory->setOrder($object);
                $this->orderStatusHistoryResource->save($statusHistory);
            }
        }
        $this->addressHandler->removeEmptyAddresses($object);
        $this->addressHandler->process($object);
    }
}
