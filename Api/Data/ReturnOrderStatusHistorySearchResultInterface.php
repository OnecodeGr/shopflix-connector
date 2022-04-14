<?php
/**
 * ReturnOrderStatusHistorySearchResultInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ReturnOrderStatusHistorySearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return ReturnOrderStatusHistoryInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param ReturnOrderStatusHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
