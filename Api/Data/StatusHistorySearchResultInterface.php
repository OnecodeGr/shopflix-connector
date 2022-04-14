<?php
/**
 * StatusHistorySearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface StatusHistorySearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return StatusHistoryInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param StatusHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
