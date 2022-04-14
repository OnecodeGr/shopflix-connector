<?php
/**
 * ItemRepository.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ItemSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderItemRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Metadata;

class ItemRepository implements ReturnOrderItemRepositoryInterface
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
        Metadata                         $metadata,
        CollectionProcessorInterface     $collectionProcessor
    )
    {
        $this->searchResultFactory = $searchResultInterfaceFactory;
        $this->metadata = $metadata;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function get(int $id)
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registry[$id])) {
            /** @var ReturnOrderItemInterface $orderItem */
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

    /**
     * @inheritDoc
     */
    public function save(ReturnOrderItemInterface $orderItem)
    {
        $this->metadata->getMapper()->save($orderItem);

        $this->registry[$orderItem->getItemId()] = $orderItem;
        return $this->registry[$orderItem->getItemId()];
    }

    /**
     * @inheritDoc
     */
    public function delete(ReturnOrderItemInterface $orderItem)
    {
        $this->metadata->getMapper()->delete($orderItem);
        unset($this->registry[$orderItem->getItemId()]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $this->collectionProcessor->process($searchCriteria, $searchResult);


        return $searchResult;
    }
}
