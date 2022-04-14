<?php
/**
 * AddressRepository.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\AddressRepositoryInterface;
use Onecode\ShopFlixConnector\Api\Data\AddressInterface;
use Onecode\ShopFlixConnector\Api\Data\AddressSearchResultInterfaceFactory as SearchResultFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Metadata;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Address\Collection;

class AddressRepository implements AddressRepositoryInterface
{


    /**
     * @var AddressInterface[]
     */
    protected $registry = [];
    private  $metadata;
    private  $searchResultFactory;
    private  $collectionProcessor;

    public function __construct(
        Metadata                     $metadata,
        SearchResultFactory          $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->metadata = $metadata;
        $this->searchResultFactory = $searchResultFactory;
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
            /** @var AddressInterface $entity */
            $entity = $this->metadata->getNewInstance()->load($id);
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(
                    __("The entity that was requested doesn't exist. Verify the entity and try again.")
                );
            }

            $this->registry[$id] = $entity;
        }

        return $this->registry[$id];
    }

    /**
     * @inheritDoc
     * @throws CouldNotSaveException
     */
    public function save(AddressInterface $orderAddress)
    {
        try {
            $this->metadata->getMapper()->save($orderAddress);
            $this->registry[$orderAddress->getEntityId()] = $orderAddress;
        } catch (Exception $e) {
            throw new CouldNotSaveException(__("The order address couldn't be saved."), $e);
        }

        return $this->registry[$orderAddress->getEntityId()];
    }


    /**
     * @inheritDoc
     * @throws CouldNotDeleteException
     */
    public function delete(AddressInterface $orderAddress)
    {
        try {
            $this->metadata->getMapper()->delete($orderAddress);

            unset($this->registry[$orderAddress->getEntityId()]);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__("The order address couldn't be deleted."), $e);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }

    /**
     * Creates new order address instance.
     *
     * @return AddressInterface
     */
    public function create()
    {
        return $this->metadata->getNewInstance();
    }
}
