<?php
/**
 * LineItemInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

interface LineItemInterface
{
    /**
     * Gets the order item ID for the item.
     *
     * @return int Order item ID.
     * @since 100.1.2
     */
    public function getOrderItemId();

    /**
     * Sets the order item ID for the item.
     *
     * @param int $id
     * @return \Onecode\ShopFlixConnector\Api\Data\LineItemInterface
     * @since 100.1.2
     */
    public function setOrderItemId($id);

    /**
     * Gets the quantity for the item.
     *
     * @return float Quantity.
     * @since 100.1.2
     */
    public function getQty();

    /**
     * Sets the quantity for the item.
     *
     * @param float $qty
     * @return $this
     * @since 100.1.2
     */
    public function setQty($qty);
}
