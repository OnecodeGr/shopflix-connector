<?php
/**
 * ReturnOrderManagementInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

interface ReturnOrderManagementInterface
{

    /**
     * Approve a specified return order.
     *
     * @param int $id The return order ID.
     * @param bool $sync
     * @return bool
     */
    public function approved(int $id , bool $sync = true): bool;

    /**
     * Decline a specified return order.
     *
     * @param int $id The return order ID.
     * @param string $message
     * @param bool $sync
     * @return bool
     */
    public function declined(int $id, string $message = '' , bool $sync = true): bool;

    /**
     * Lists comments for a specified order.
     *
     * @param int $id The order ID.
     * @return Data\ReturnOrderStatusHistorySearchResultInterface Order status history
     * search results interface.
     */
    public function getCommentsList(int $id): Data\ReturnOrderStatusHistorySearchResultInterface;

    /**
     * Adds a comment to a specified return order.
     *
     * @param int $id The return order ID.
     * @param Data\ReturnOrderStatusHistoryInterface $statusHistory Status history comment.
     * @return bool
     */
    public function addComment(int $id, Data\ReturnOrderStatusHistoryInterface $statusHistory): bool;

    /**
     * Gets the status for a specified return order.
     *
     * @param int $id The order ID.
     * @return string Order status.
     */
    public function getStatus(int $id): string;

    /**
     *  Complete specified order.
     *
     * @param int $id The return order ID.
     * @return bool
     */
    public function completed(int $id): bool;

    /**
     *  On the way a specified return order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function onTheWay(int $id): bool;

    /**
     * Delivered a specified return order
     * @param int $id
     * @return bool
     */
    public function delivered(int $id): bool;
}
