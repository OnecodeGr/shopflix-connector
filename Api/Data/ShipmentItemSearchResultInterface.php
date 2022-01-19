<?php
/**
 * ShipmentItemSearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

interface ShipmentItemSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Onecode\ShopFlixConnector\Api\Data\ShipmentItemInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Onecode\ShopFlixConnector\Api\Data\ShipmentItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
