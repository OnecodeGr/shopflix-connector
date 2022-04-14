<?php
/**
 * ReturnOrderStatusHistoryRepositoryInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistorySearchResultInterface;

interface ReturnOrderStatusHistoryRepositoryInterface
{
    /**
     * Lists order status history comments that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return ReturnOrderStatusHistorySearchResultInterface Order status history
     * search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified order status comment.
     *
     * @param int $id The order status comment ID.
     * @return ReturnOrderStatusHistoryInterface Order status history interface.
     */
    public function get($id);

    /**
     * Deletes a specified order status comment.
     *
     * @param ReturnOrderStatusHistoryInterface $entity The order status comment.
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ReturnOrderStatusHistoryInterface $entity);

    /**
     * Performs persist operations for a specified order status comment.
     *
     * @param ReturnOrderStatusHistoryInterface $entity The order status comment.
     * @return ReturnOrderStatusHistoryInterface Order status history interface.
     * @throws CouldNotSaveException
     */
    public function save(ReturnOrderStatusHistoryInterface $entity): ReturnOrderStatusHistoryInterface;
}
