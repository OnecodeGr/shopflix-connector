<?php
/**
 * StatusHistoryRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Onecode\ShopFlixConnector\Api\Data\StatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusHistorySearchResultInterface;

interface StatusHistoryRepositoryInterface
{
    /**
     * Lists order status history comments that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return StatusHistorySearchResultInterface Order status history
     * search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified order status comment.
     *
     * @param int $id The order status comment ID.
     * @return StatusHistoryInterface Order status history interface.
     */
    public function get($id);

    /**
     * Deletes a specified order status comment.
     *
     * @param StatusHistoryInterface $entity The order status comment.
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(StatusHistoryInterface $entity);

    /**
     * Performs persist operations for a specified order status comment.
     *
     * @param StatusHistoryInterface $entity The order status comment.
     * @return StatusHistoryInterface Order status history interface.
     * @throws CouldNotSaveException
     */
    public function save(StatusHistoryInterface $entity);
}
