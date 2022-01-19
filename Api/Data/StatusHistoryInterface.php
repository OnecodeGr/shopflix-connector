<?php
/**
 * StatusHistoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api\Data;

interface StatusHistoryInterface
{
    const ENTITY_ID = 'entity_id';
    const PARENT_ID = 'parent_id';
    const COMMENT = 'comment';
    const STATUS = 'status';
    const IS_SHOPFLIX = "is_shopflix";
    const CREATED_AT = 'created_at';

    /**
     * Gets the comment for the order status history.
     *
     * @return string Comment.
     */
    public function getComment(): string;

    /**
     * Gets the created-at timestamp for the order status history.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(): ?string;

    /**
     * Sets the created-at timestamp for the order status history.
     *
     * @param string $createdAt timestamp
     * @return $this
     */
    public function setCreatedAt(string $createdAt): StatusHistoryInterface;

    /**
     * Gets the ID for the order status history.
     *
     * @return int|null Order status history ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Gets the parent ID for the order status history.
     *
     * @return int|null Parent ID.
     */
    public function getParentId(): ?int;

    /**
     * Gets the status for the order status history.
     *
     * @return string|null Status.
     */
    public function getStatus(): ?string;

    /**
     * Gets the flag for sync in shopflix for the order status history.
     * @return bool|null
     */
    public function getIsShopFlix(): ?bool;

    /**
     * Sets the parent ID for the order status history.
     *
     * @param int $id
     * @return $this
     */
    public function setParentId(int $id): StatusHistoryInterface;


    /**
     * Sets the comment for the order status history.
     *
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment): StatusHistoryInterface;

    /**
     * Sets the status for the order status history.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): StatusHistoryInterface;

    /**
     * Sets the flag for syncing the order history.
     *
     * @param bool $isShopFlix
     * @return $this
     */
    public function setIsShopFlix(bool $isShopFlix): StatusHistoryInterface;

}
