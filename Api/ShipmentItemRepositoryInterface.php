<?php
/**
 * ShipmentItemRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\ShipmentItemInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentItemSearchResultInterface;

interface ShipmentItemRepositoryInterface
{
    /**
     * Lists shipment items that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return ShipmentItemSearchResultInterface Shipment item search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified shipment item.
     *
     * @param int $id
     * @return ShipmentItemInterface
     */
    public function get($id);

    /**
     * Deletes a specified shipment item.
     *
     * @param ShipmentItemInterface $entity The shipment item.
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentItemInterface $entity);

    /**
     * Performs persist operations for a specified shipment item.
     *
     * @param ShipmentItemInterface $entity The shipment item.
     * @return ShipmentItemInterface Shipment interface.
     * @throws CouldNotSaveException
     */
    public function save(ShipmentItemInterface $entity);
}
