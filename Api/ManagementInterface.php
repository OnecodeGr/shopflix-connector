<?php
/**
 * ManagementInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Onecode\ShopFlixConnector\Api\Data\StatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusHistorySearchResultInterface;

interface ManagementInterface
{


    /**
     * Accepts a specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function accept(int $id): bool;

    /**
     * Cancels a specified order.
     *
     * @param int $id The order ID.
     * @param string $message
     * @return bool
     */
    public function reject(int $id, string $message = ''): bool;

    /**
     * Lists comments for a specified order.
     *
     * @param int $id The order ID.
     * @return StatusHistorySearchResultInterface Order status history
     * search results interface.
     */
    public function getCommentsList(int $id): Data\StatusHistorySearchResultInterface;

    /**
     * Adds a comment to a specified order.
     *
     * @param int $id The order ID.
     * @param StatusHistoryInterface $statusHistory Status history comment.
     * @return bool
     */
    public function addComment(int $id, StatusHistoryInterface $statusHistory): bool;


    /**
     * Gets the status for a specified order.
     *
     * @param int $id The order ID.
     * @return string Order status.
     */
    public function getStatus(int $id): string;

    /**
     * Ready to be shipped a specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function readyToBeShipped(int $id): bool;

    /**
     * Cancel a specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function cancel(int $id): bool;

    /**
     * Partial Shipped a specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function partialShipped(int $id): bool;
    /**
     *  Shipped a specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function shipped(int $id): bool;

    /**
     *  On the way a specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function onTheWay(int $id): bool;

    /**
     *  Complete specified order.
     *
     * @param int $id The order ID.
     * @return bool
     */
    public function completed(int $id):bool;
}
