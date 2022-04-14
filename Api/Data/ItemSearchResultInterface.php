<?php
/**
 * ItemSearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ItemSearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return ItemInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
