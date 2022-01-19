<?php
/**
 * HistoryRepository.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Status;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\StatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusHistoryInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\StatusHistorySearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\StatusHistoryRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Spi\StatusHistoryResourceInterface;


class HistoryRepository implements StatusHistoryRepositoryInterface
{

    /**
     * @var StatusHistoryResourceInterface
     */
    private $historyResource;

    /**
     * @var StatusHistoryInterfaceFactory
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
     * @param StatusHistoryResourceInterface $historyResource
     * @param StatusHistoryInterfaceFactory $historyFactory
     * @param StatusHistorySearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        StatusHistoryResourceInterface            $historyResource,
        StatusHistoryInterfaceFactory                  $historyFactory,
        StatusHistorySearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface                   $collectionProcessor
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
    public function delete(StatusHistoryInterface $entity)
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
    public function save(StatusHistoryInterface $entity)
    {
        try {
            $this->historyResource->save($entity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not save the order status history.'), $e);
        }
        return $entity;
    }
}
