<?php
/**
 * ItemRepository.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Shipment;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\ShipmentItemInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentItemInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\ShipmentItemSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\ShipmentItemRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Spi\ShipmentItemResourceInterface;

class ItemRepository implements ShipmentItemRepositoryInterface
{

    /**
     * @var ShipmentItemResourceInterface
     */
    private $itemResource;

    /**
     * @var ShipmentItemInterfaceFactory
     */
    private $itemFactory;

    /**
     * @var ShipmentItemSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        ShipmentItemResourceInterface            $itemResource,
        ShipmentItemInterfaceFactory             $itemFactory,
        ShipmentItemSearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface             $collectionProcessor)
    {
        $this->itemResource = $itemResource;
        $this->itemFactory = $itemFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    public function get($id)
    {
        $entity = $this->itemFactory->create();
        $this->itemResource->load($entity, $id);
        return $entity;
    }

    public function delete(ShipmentItemInterface $entity)
    {
        try {
            $this->itemResource->delete($entity);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the shipment item.'), $e);
        }
        return true;
    }

    public function save(ShipmentItemInterface $entity)
    {
        try {
            $this->itemResource->save($entity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the shipment item.'), $e);
        }
        return $entity;
    }
}
