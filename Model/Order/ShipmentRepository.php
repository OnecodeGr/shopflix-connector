<?php
/**
 * ShipmentRepository.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
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
use Onecode\ShopFlixConnector\Api\Data\ShipmentSearchResultInterfaceFactory as SearchResultFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Metadata;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentSearchResultInterface;
use Onecode\ShopFlixConnector\Api\ShipmentRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Collection;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var \Onecode\ShopFlixConnector\Api\Data\ShipmentInterface[]
     */
    protected $registry = [];

    /**
     * @var \Onecode\ShopFlixConnector\Api\Data\ShipmentInterface[]
     */
    protected $registryIncrementID = [];
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param Metadata $metadata
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        Metadata                     $metadata,
        SearchResultFactory          $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null
    )
    {
        $this->metadata = $metadata;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
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

    public function deleteById($id)
    {
        $entity = $this->get($id);

        return $this->delete($entity);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }

        if (!isset($this->registry[$id])) {
            /** @var \Onecode\ShopFlixConnector\Api\Data\ShipmentInterface $entity */
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


    public function getByIncrementId($incrementId){
        if (!$incrementId) {
            throw new InputException(__('An increment id is needed. Set the increment id  and try again.'));
        }

        if (!isset($this->registryIncrementID[$incrementId])) {
            /** @var \Onecode\ShopFlixConnector\Api\Data\ShipmentInterface $entity */
            $entity = $this->metadata->getNewInstance()->load($incrementId , "increment_id");
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(
                    __("The entity that was requested doesn't exist. Verify the entity and try again.")
                );
            }

            $this->registryIncrementID[$incrementId] = $entity;
        }

        return $this->registryIncrementID[$incrementId];
    }

    /**
     * @inheritDoc
     */
    public function delete(ShipmentInterface $entity)
    {
        try {
            $this->metadata->getMapper()->delete($entity);

            unset($this->registry[$entity->getEntityId()]);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__("The shipment couldn't be deleted."), $e);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(ShipmentInterface $entity)
    {
        try {
            $this->metadata->getMapper()->save($entity);
            $this->registry[$entity->getEntityId()] = $entity;
        } catch (Exception $e) {
            throw new CouldNotSaveException(__("The shipment couldn't be saved."), $e);
        }

        return $this->registry[$entity->getEntityId()];
    }

    /**
     * @inheritDoc
     */
    public function create()
    {
        return $this->metadata->getNewInstance();
    }

    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class
            );
        }
        return $this->collectionProcessor;
    }
}
