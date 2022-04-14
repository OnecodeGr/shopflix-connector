<?php
/**
 * ReturnOrderItemRepositoryInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemSearchResultInterface;

interface ReturnOrderItemRepositoryInterface
{
    /**
     * @param int $id
     * @return ReturnOrderItemInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id);


    /**
     * @param ReturnOrderItemInterface $orderItem
     * @return ReturnOrderItemInterface
     */
    public function save(ReturnOrderItemInterface $orderItem);

    /**
     * @param ReturnOrderItemInterface $orderItem
     * @return void
     */
    public function delete(ReturnOrderItemInterface $orderItem);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReturnOrderItemSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
