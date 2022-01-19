<?php
/**
 * OrderSearchResultInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return  OrderInterface[]
     */
    public function getItems();

    /**
     * @param OrderInterface[] $items
     * @return void
     */
    public function setItems(array $items);

}
