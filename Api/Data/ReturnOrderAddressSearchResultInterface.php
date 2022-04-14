<?php
/**
 * ReturnOrderAddressSearchResultInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ReturnOrderAddressSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return  ReturnOrderAddressInterface[]
     */
    public function getItems();

    /**
     * @param ReturnOrderAddressInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
