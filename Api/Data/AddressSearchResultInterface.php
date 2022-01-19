<?php
/**
 * AddressSearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AddressSearchResultInterface extends SearchResultsInterface

{
    /**
     * @return  AddressInterface[]
     */
    public function getItems();

    /**
     * @param AddressInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
