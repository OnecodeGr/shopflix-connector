<?php
/**
 * TrackRepository.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Shipment;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterfaceFactory;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackSearchResultInterfaceFactory;
use Onecode\ShopFlixConnector\Api\ShipmentTrackRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Onecode\ShopFlixConnector\Model\Spi\ShipmentTrackResourceInterface;
use Psr\Log\LoggerInterface;

class TrackRepository implements ShipmentTrackRepositoryInterface
{

    /**
     * @var ShipmentTrackResourceInterface
     */
    private $trackResource;

    /**
     * @var ShipmentTrackInterfaceFactory
     */
    private $trackFactory;

    /**
     * @var ShipmentTrackSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    private $shipmentCollection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ShipmentTrackResourceInterface $trackResource
     * @param ShipmentTrackInterfaceFactory $trackFactory
     * @param ShipmentTrackSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory|null $shipmentCollection
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ShipmentTrackResourceInterface            $trackResource,
        ShipmentTrackInterfaceFactory             $trackFactory,
        ShipmentTrackSearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface              $collectionProcessor,
        CollectionFactory                         $shipmentCollection = null,
        LoggerInterface                           $logger = null
    )
    {
        $this->trackResource = $trackResource;
        $this->trackFactory = $trackFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->shipmentCollection = $shipmentCollection ?:
            ObjectManager::getInstance()->get(CollectionFactory::class);
        $this->logger = $logger ?:
            ObjectManager::getInstance()->get(LoggerInterface::class);
    }


    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $entity = $this->trackFactory->create();
        $this->trackResource->load($entity, $id);
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(ShipmentTrackInterface $entity)
    {
        try {
            $this->trackResource->delete($entity);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the shipment tracking.'), $e);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(ShipmentTrackInterface $entity)
    {
        $shipments = $this->shipmentCollection->create()
            ->addFieldToFilter('order_id', $entity['order_id'])
            ->addFieldToFilter('entity_id', $entity['parent_id'])
            ->toArray();

        if (empty($shipments['items'])) {
            $this->logger->error('The shipment doesn\'t belong to the order.');
            throw new CouldNotSaveException(__('Could not save the shipment tracking.'));
        }

        try {
            $this->trackResource->save($entity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the shipment tracking.'), $e);
        }
        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        return $this->delete($entity);
    }
}
