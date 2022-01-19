<?php
/**
 * ItemRepository.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ItemInterface;
use Onecode\ShopFlixConnector\Api\Data\ItemSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\ItemRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Metadata;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item\Collection;

class ItemRepository implements ItemRepositoryInterface
{


    /**
     * @var array
     */
    private $registry = [];
    /**
     * @var Metadata
     */
    private $metadata;
    /**
     * @var ItemSearchResultInterfaceFactory
     */
    private $searchResultFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        ItemSearchResultInterfaceFactory $searchResultInterfaceFactory,
        Metadata                              $metadata,
        CollectionProcessorInterface          $collectionProcessor
    )
    {
        $this->searchResultFactory = $searchResultInterfaceFactory;
        $this->metadata = $metadata;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(ItemInterface $entity): ItemInterface
    {

        $this->metadata->getMapper()->save($entity);

        $this->registry[$entity->getItemId()] = $entity;
        return $this->registry[$entity->getItemId()];
    }

    /**
     * Register entity to delete
     *
     * @param ItemInterface $entity
     * @return bool
     * @throws Exception
     */
    public function delete(ItemInterface $entity): bool
    {
        $this->metadata->getMapper()->delete($entity);
        unset($this->registry[$entity->getItemId()]);
        return true;
    }

    /**
     * Find entities by criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ItemInterface[]
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $this->collectionProcessor->process($searchCriteria, $searchResult);


        return $searchResult;
    }

    /**
     * Set parent item.
     *
     * @param ItemInterface $orderItem
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function addParentItem(ItemInterface $orderItem)
    {
        if ($parentId = $orderItem->getParentItemId()) {
            $orderItem->setParentItem($this->get($parentId));
        } else {
            $orderCollection = $orderItem->getOrder()->getItemsCollection()->filterByParent($orderItem->getItemId());

            foreach ($orderCollection->getItems() as $item) {
                if ($item->getParentItemId() === $orderItem->getId()) {
                    $item->setParentItem($orderItem);
                }
            }
        }
    }

    public function get(int $id)
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registry[$id])) {
            /** @var ItemInterface $orderItem */
            $orderItem = $this->metadata->getNewInstance()->load($id);
            if (!$orderItem->getItemId()) {
                throw new NoSuchEntityException(
                    __("The entity that was requested doesn't exist. Verify the entity and try again.")
                );
            }


            $this->registry[$id] = $orderItem;
        }
        return $this->registry[$id];
    }

}
