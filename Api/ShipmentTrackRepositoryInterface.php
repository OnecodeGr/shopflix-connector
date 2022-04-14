<?php
/**
 * ShipmentTrackRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackSearchResultInterface;

interface ShipmentTrackRepositoryInterface
{
    /**
     * Lists shipment items that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return ShipmentTrackSearchResultInterface Shipment item search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified shipment item.
     *
     * @param int $id
     * @return ShipmentTrackInterface
     */
    public function get($id);

    /**
     * Deletes a specified shipment item.
     *
     * @param ShipmentTrackInterface $entity The shipment item.
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentTrackInterface $entity);

    /**
     * Performs persist operations for a specified shipment item.
     *
     * @param ShipmentTrackInterface $entity The shipment item.
     * @return ShipmentTrackInterface Shipment interface.
     * @throws CouldNotSaveException
     */
    public function save(ShipmentTrackInterface $entity);

    /**
     * Deletes a specified shipment track by ID.
     *
     * @param int $id The shipment track ID.
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}

