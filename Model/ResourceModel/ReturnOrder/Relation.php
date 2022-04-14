<?php
/**
 * Relation.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderItemRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Handler\Address as AddressHandler;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Status\History as StatusHistoryResource;
use Onecode\ShopFlixConnector\Model\ReturnOrder;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Item;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Status\History;

class Relation implements RelationInterface
{
    /**
     * @var AddressHandler
     */
    protected $addressHandler;

    /**
     * @var ReturnOrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var StatusHistoryResource
     */
    protected $orderStatusHistoryResource;


    /**
     * @param AddressHandler $addressHandler
     * @param ReturnOrderItemRepositoryInterface $orderItemRepository
     * @param StatusHistoryResource $orderStatusHistoryResource
     */
    public function __construct(
        AddressHandler                     $addressHandler,
        ReturnOrderItemRepositoryInterface $orderItemRepository,
        StatusHistoryResource              $orderStatusHistoryResource
    )
    {
        $this->addressHandler = $addressHandler;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderStatusHistoryResource = $orderStatusHistoryResource;
    }

    /**
     * @inheritDoc
     */
    public function processRelation(AbstractModel $object)
    {
        /** @var ReturnOrder $object */
        if (null !== $object->getItems()) {
            /** @var Item $item */
            foreach ($object->getItems() as $item) {
                $item->setOrderId($object->getId());
                $item->setOrder($object);
                $this->orderItemRepository->save($item);
            }
        }

        if($object->getStatus() == "on_the_way_to_the_store") {
            #dd($object->getStatusHistories());
        }

        if (null !== $object->getStatusHistories()) {

            /** @var History $statusHistory */
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
