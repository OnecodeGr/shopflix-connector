<?php
/**
 * ShipmentRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentSearchResultInterface;

interface ShipmentRepositoryInterface
{
    /**
     * Lists shipments that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return ShipmentSearchResultInterface Shipment search results interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified shipment.
     *
     * @param int $id The shipment ID.
     * @return ShipmentInterface
     */
    public function get($id);
    /**
     * Loads a specified shipment.
     *
     * @param string $incrementId The shipment Increment ID.
     * @return ShipmentInterface
     */
    public function getByIncrementId($incrementId);

    /**
     * Deletes a specified shipment.
     *
     * @param ShipmentInterface $entity The shipment.
     * @return bool
     */
    public function delete(ShipmentInterface $entity);

    /**
     * Performs persist operations for a specified shipment.
     *
     * @param ShipmentInterface $entity The shipment.
     * @return ShipmentInterface Shipment interface.
     */
    public function save(ShipmentInterface $entity);

    /**
     * Creates new shipment instance.
     *
     * @return ShipmentInterface Shipment interface.
     */
    public function create();
}
