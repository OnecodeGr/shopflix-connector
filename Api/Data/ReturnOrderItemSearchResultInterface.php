<?php
/**
 * ReturnOrderItemSearchResultInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ReturnOrderItemSearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return ReturnOrderItemInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param ReturnOrderItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
