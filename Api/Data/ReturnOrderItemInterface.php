<?php
/**
 * ReturnOrderItemInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;


interface ReturnOrderItemInterface
{
    const ITEM_ID = "item_id";
    const SKU = "sku";
    const PRICE = "price";
    const NAME = "name";
    const QTY = "qty";
    const PRODUCT_TYPE = "product_type";
    const PRODUCT_ID = "product_id";
    const PARENT_ITEM_ID = "parent_item_id";
    const ORDER_ID = "parent_id";
    const RETURN_REASON = "return_reason";

    const ATTRIBUTES = [
        self::SKU,
        self::PRICE,
        self::NAME,
        self::QTY,
        self::PRODUCT_TYPE,
        self::PRODUCT_ID,
        self::PARENT_ITEM_ID,
        self::ORDER_ID,
        self::RETURN_REASON
    ];



    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Gets the item ID for the order item.
     *
     * @return int|null Item ID.
     */
    public function getItemId(): ?int;


    /**
     * Sets the item ID for the order item.
     *
     * @param int $id
     * @return ReturnOrderItemInterface
     */
    public function setItemId(int $id): ReturnOrderItemInterface;

    /**
     * @param int $orderId
     * @return ReturnOrderItemInterface
     */
    public function setOrderId(int $orderId): ReturnOrderItemInterface;


    /**
     * @return int
     */
    public function getParentItemId(): int;

    /**
     * @param int $parentItemId
     * @return ReturnOrderItemInterface
     */
    public function setParentItemId(int $parentItemId): ReturnOrderItemInterface;

    /**
     * @return int
     */
    public function getProductId(): int;


    /**
     * @param int $productId
     * @return ReturnOrderItemInterface
     */
    public function setProductId(int $productId): ReturnOrderItemInterface;

    /**
     * @return string
     */
    public function getProductType(): string;

    /**
     * @param string $productType
     * @return ReturnOrderItemInterface
     */
    public function setProductType(string $productType): ReturnOrderItemInterface;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @param string $sku
     * @return ReturnOrderItemInterface
     */
    public function setSku(string $sku): ReturnOrderItemInterface;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @param float $price
     * @return ReturnOrderItemInterface
     */
    public function setPrice(float $price): ReturnOrderItemInterface;

    /**
     * @return int
     */
    public function getQty(): int;

    /**
     * @param int $qty
     * @return ReturnOrderItemInterface
     */
    public function setQty(int $qty): ReturnOrderItemInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return ReturnOrderItemInterface
     */
    public function setName(string $name): ReturnOrderItemInterface;

    /**
     * @return string
     */
    public function getReturnReason(): string;

    /**
     * @param string $returnReason
     * @return ReturnOrderItemInterface
     */
    public function setReturnReason(string $returnReason): ReturnOrderItemInterface;


}
