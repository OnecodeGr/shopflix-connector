<?php
/**
 * ReturnOrderInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

interface ReturnOrderInterface
{
    const ENTITY_ID = "entity_id";
    const SHOPFLIX_ORDER_ID = "shopflix_order_id";
    const SHOPFLIX_PARENT_ORDER_ID = "shopflix_parent_order_id";
    const PARENT_ORDER_ID = "parent_id";
    const INCREMENT_ID = "increment_id";
    const STATUS = "status";
    const SUBTOTAL = "subtotal";
    const TOTAL_PAID = "total_paid";
    const CUSTOMER_EMAIL = "customer_email";
    const CUSTOMER_FIRSTNAME = "customer_firstname";
    const CUSTOMER_LASTNAME = "customer_lastname";
    const CUSTOMER_REMOTE_IP = "customer_remote_ip";
    const CUSTOMER_NOTE = "customer_note";
    const SHIPPING_ADDRESS_ID = "shipping_address_id";
    const BILLING_ADDRESS_ID = "billing_address_id";
    const SYNC = "sync";

    const CREATED_AT = "created_at";
    const UPDATED_AT = 'updated_at';

    const ITEMS = "items";
    const STATE = "state";

    const STATE_PROCESS_FROM_SHOPFLIX = "process_from_shopflix";
    const STATE_DELIVERED_TO_THE_STORE = "delivered";
    const STATE_APPROVED = "approved";
    const STATE_DECLINED = "declined";

    const STATUS_HISTORIES = 'status_histories';

    const ATTRIBUTES = [
        self::SHOPFLIX_ORDER_ID,
        self::SHOPFLIX_PARENT_ORDER_ID,
        self::PARENT_ORDER_ID,
        self::INCREMENT_ID,
        self::STATUS,
        self::SUBTOTAL,
        self::TOTAL_PAID,
        self::CUSTOMER_EMAIL,
        self::CUSTOMER_FIRSTNAME,
        self::CUSTOMER_LASTNAME,
        self::CUSTOMER_REMOTE_IP,
        self::CUSTOMER_NOTE,
    ];

    /**
     * Gets Shopflix Order id
     * @return int
     */
    public function getShopFlixOrderId(): int;

    /**
     * @param int $shopflixOrderId
     * @return ReturnOrderInterface
     */
    public function setShopFlixOrderId(int $shopflixOrderId): ReturnOrderInterface;

    /**
     * @return int
     */
    public function getParentId(): int;

    /**
     * @param int $parentId
     * @return ReturnOrderInterface
     */
    public function setParentId(int $parentId): ReturnOrderInterface;

    /**
     * @return int
     */
    public function getShopFlixParentOrderId(): int;

    /**
     * @param int $shopflixParentOrderId
     * @return ReturnOrderInterface
     */
    public function setShopFlixParentOrderId(int $shopflixParentOrderId): ReturnOrderInterface;

    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @param string $state
     * @return ReturnOrderInterface
     */
    public function setState(string $state): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     * @return ReturnOrderInterface
     */
    public function setStatus(string $status): ReturnORderInterface;

    /**
     * @return float
     */
    public function getSubtotal(): float;

    /**
     * @param float $subtotal
     * @return ReturnOrderInterface
     */
    public function setSubtotal(float $subtotal): ReturnOrderInterface;

    /**
     * @return float
     */
    public function getTotalPaid(): float;

    /**
     * @param float $totalPaid
     * @return ReturnOrderInterface
     */
    public function setTotalPaid(float $totalPaid): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getIncrementId(): string;

    /**
     * @param string $incrementId
     * @return ReturnOrderInterface
     */
    public function setIncrementId(string $incrementId): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * @param string $customerEmail
     * @return ReturnOrderInterface
     */
    public function setCustomerEmail(string $customerEmail): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getCustomerFirstname(): string;

    /**
     * @param string $customerFirstname
     * @return ReturnOrderInterface
     */
    public function setCustomerFirstname(string $customerFirstname): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getCustomerLastname(): string;

    /**
     * @param string $customerLastname
     * @return ReturnOrderInterface
     */
    public function setCustomerLastname(string $customerLastname): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getRemoteIp(): ?string;

    /**
     * @param string $remoteIp
     * @return ReturnOrderInterface
     */
    public function setRemoteIP(string $remoteIp): ReturnOrderInterface;

    /**
     * @return string
     */
    public function getCustomerNote(): string;

    /**
     * @return ReturnOrderInterface
     */
    public function setCustomerNote(): ReturnOrderInterface;

    /**
     * @return int|null
     */
    public function getShippingAddressId(): ?int;

    /**
     * @param int $shippingAddressId
     * @return ReturnOrderInterface
     */
    public function setShippingAddressId(int $shippingAddressId): ReturnOrderInterface;

    /**
     * @return int|null
     */
    public function getBillingAddressId(): ?int;

    /**
     * @param int $billingAddressId
     * @return ReturnOrderInterface
     */
    public function setBillingAddressId(int $billingAddressId): ReturnOrderInterface;

    /**
     * Gets the created-at timestamp for the order.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(): ?string;

    /**
     * Sets the created-at timestamp for the order.
     *
     * @param string $createdAt timestamp
     * @return ReturnOrderInterface
     */
    public function setCreatedAt(string $createdAt): ReturnOrderInterface;

    /**
     * Gets the updated-at timestamp for the order.
     *
     * @return string|null Created-at timestamp.
     */
    public function getUpdatedAt(): ?string;

    /**
     * Sets the updated-at timestamp for the order.
     *
     * @param string $updatedAt timestamp
     * @return ReturnOrderInterface
     */
    public function setUpdatedAt(string $updatedAt): ReturnOrderInterface;

    /**
     * @param ReturnOrderAddressInterface|null $billingAddress
     * @return ReturnOrderInterface
     */
    public function setBillingAddress(ReturnOrderAddressInterface $billingAddress = null): ReturnOrderInterface;

    /**
     * @param ReturnOrderAddressInterface|null $shippingAddress
     * @return ReturnOrderInterface
     */
    public function setShippingAddress(ReturnOrderAddressInterface $shippingAddress = null): ReturnOrderInterface;

    /**
     * @param bool $synced
     * @return ReturnOrderInterface
     */
    public function setSynced(bool $synced): ReturnOrderInterface;

    /**
     * @return bool
     */
    public function getSynced(): bool;
}
