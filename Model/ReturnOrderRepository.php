<?php
/**
 * ReturnOrderRepository.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderSearchResultInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\ReturnOrderRepositoryInterface as ReturnOrderRepositoryInterfaceAlias;
use Onecode\ShopFlixConnector\Model\ResourceModel\Metadata;

class ReturnOrderRepository implements ReturnOrderRepositoryInterfaceAlias
{


    private $registry = [];
    private $registryIncrementId = [];
    private $searchResultFactory;
    private $collectionProcessor;
    private $metadata;

    public function __construct(
        ReturnOrderSearchResultInterfaceFactory $searchResultInterfaceFactory,
        Metadata                          $metadata,
        CollectionProcessorInterface      $collectionProcessor
    )
    {
        $this->searchResultFactory = $searchResultInterfaceFactory;
        $this->metadata = $metadata;
        $this->collectionProcessor = $collectionProcessor;
    }
    /**
     * @inheritDoc
     */
    public function getById(int $id): ReturnOrderInterface
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registry[$id])) {
            /** @var OrderInterface $order */
            $order = $this->metadata->getNewInstance()->load($id);
            if (!$order->getId()) {
                throw new NoSuchEntityException(
                    __('Unable to find shopflix order with id "%1"', $id)
                );
            }


            $this->registry[$id] = $order;
        }
        return $this->registry[$id];
    }

    /**
     * @inheritDoc
     */
    public function getByIncrementId(string $incrementId): ReturnOrderInterface
    {
        if (!$incrementId) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registryIncrementId[$incrementId])) {
            /** @var OrderInterface $order */
            $order = $this->metadata->getNewInstance()->load($incrementId, ReturnOrderInterface::SHOPFLIX_ORDER_ID);
            if (!$order->getId()) {
                throw new NoSuchEntityException(
                    __('Unable to find shopflix return order with increment id "%1"', $incrementId)
                );
            }
            $this->registryIncrementId[$incrementId] = $order;
        }
        return $this->registryIncrementId[$incrementId];
    }

    /**
     * @inheritDoc
     */
    public function save(ReturnOrderInterface $order): ReturnOrderInterface
    {
        $this->metadata->getMapper()->save($order);

        $this->registry[$order->getId()] = $order;
        return $this->registry[$order->getId()];
    }

    /**
     * @inheritDoc
     */
    public function delete(ReturnOrderInterface $order): ReturnOrderInterface
    {
        $this->metadata->getMapper()->delete($order);
        unset($this->registry[$order->getId()]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReturnOrderSearchResultInterface
    {
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $this->collectionProcessor->process($searchCriteria, $searchResults);


        return $searchResults;
    }
}
