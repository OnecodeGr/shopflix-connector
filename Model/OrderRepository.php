<?php
/**
 * OrderRepository.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderSearchResultInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{

    private $orderFactory;

    private $searchResultFactory;
    private $collectionProcessor;

    public function __construct(OrderFactory                      $orderFactory,
                                OrderSearchResultInterfaceFactory $orderSearchResultInterfaceFactory,
                                CollectionProcessorInterface      $collectionProcessorInterface)
    {
        $this->orderFactory = $orderFactory;

        $this->searchResultFactory = $orderSearchResultInterfaceFactory;
        $this->collectionProcessor = $collectionProcessorInterface;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        $order = $this->orderFactory->create();
        $order->getResource()->load($order, $id);
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Unable to find shopflix order with id "%1"', $id));
        }
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function getByIncrementId(string $incrementId)
    {
        $order = $this->orderFactory->create();
        $order->getResource()->load($order, $incrementId, OrderInterface::INCREMENT_ID);
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Unable to find shopflix order with increment id "%1"', $incrementId));
        }
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function save(OrderInterface $order)
    {
        $order->getResource()->save($order);
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function delete(OrderInterface $order)
    {
        $order->getResource()->delete($order);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderSearchResultInterface
    {
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $this->collectionProcessor->process($searchCriteria, $searchResults);


        return $searchResults;
    }
}
