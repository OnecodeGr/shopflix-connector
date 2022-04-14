<?php
/**
 * ShipmentTrackSearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ShipmentTrackSearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
