<?php
/**
 * ShipmentSearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ShipmentSearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Onecode\ShopFlixConnector\Api\Data\ShipmentInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Onecode\ShopFlixConnector\Api\Data\ShipmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
