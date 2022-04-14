<?php
/**
 * HistoryRepository.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder\Status;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistoryInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\StatusHistorySearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\ReturnOrderStatusHistoryRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Spi\ReturnOrderStatusHistoryResourceInterface;


class HistoryRepository implements ReturnOrderStatusHistoryRepositoryInterface
{

    /**
     * @var ReturnOrderStatusHistoryResourceInterface
     */
    private $historyResource;

    /**
     * @var ReturnOrderStatusHistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @var StatusHistorySearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ReturnOrderStatusHistoryResourceInterface $historyResource
     * @param ReturnOrderStatusHistoryInterfaceFactory $historyFactory
     * @param StatusHistorySearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ReturnOrderStatusHistoryResourceInterface $historyResource,
        ReturnOrderStatusHistoryInterfaceFactory  $historyFactory,
        StatusHistorySearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface              $collectionProcessor
    )
    {

        $this->historyResource = $historyResource;
        $this->historyFactory = $historyFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $entity = $this->historyFactory->create();
        $this->historyResource->load($entity, $id);
        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function delete(ReturnOrderStatusHistoryInterface $entity)
    {
        try {
            $this->historyResource->delete($entity);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the order status history.'), $e);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function save(ReturnOrderStatusHistoryInterface $entity): ReturnOrderStatusHistoryInterface
    {
        try {
            $this->historyResource->save($entity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not save the order status history.'), $e);
        }
        return $entity;
    }
}
