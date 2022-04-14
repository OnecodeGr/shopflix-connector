<?php
/**
 * ReturnOrderSearchResultInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ReturnOrderSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return  ReturnOrderInterface[]
     */
    public function getItems(): array;

    /**
     * @param ReturnOrderInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
