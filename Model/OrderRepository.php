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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ItemInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderSearchResultInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Metadata;

class OrderRepository implements OrderRepositoryInterface
{

    private $registry = [];
    private $registryMagentoOrderId = [];
    private $registryIncrementId = [];
    private $searchResultFactory;
    private $collectionProcessor;
    private $metadata;

    public function __construct(
        OrderSearchResultInterfaceFactory $searchResultInterfaceFactory,
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
    public function getById(int $id)
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

    public function getByMagentoOrderId(int $id)
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registryMagentoOrderId[$id])) {
            /** @var OrderInterface $order */
            $order = $this->metadata->getNewInstance()->load($id, OrderInterface::MAGENTO_ORDER_ID);
            if (!$order->getId()) {
                throw new NoSuchEntityException(
                    __('Unable to find shopflix order with magento order "%1"', $id)
                );
            }
            $this->registryMagentoOrderId[$id] = $order;
        }
        return $this->registryMagentoOrderId[$id];
    }

    /**
     * @inheritDoc
     */
    public function getByIncrementId(string $incrementId)
    {
        if (!$incrementId) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registryIncrementId[$incrementId])) {
            /** @var OrderInterface $order */
            $order = $this->metadata->getNewInstance()->load($incrementId, OrderInterface::SHOPFLIX_ORDER_ID);
            if (!$order->getId()) {
                throw new NoSuchEntityException(
                    __('Unable to find shopflix order with increment id "%1"', $incrementId)
                );
            }
            $this->registryIncrementId[$incrementId] = $order;
        }
        return $this->registryIncrementId[$incrementId];

    }

    /**
     * @inheritDoc
     */
    public function save(OrderInterface $order)
    {
        $this->metadata->getMapper()->save($order);

        $this->registry[$order->getId()] = $order;
        return $this->registry[$order->getId()];
    }

    /**
     * @inheritDoc
     */
    public function delete(OrderInterface $order)
    {
        $this->metadata->getMapper()->delete($order);
        unset($this->registry[$order->getId()]);
        return true;
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
