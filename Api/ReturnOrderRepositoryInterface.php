<?php
/**
 * ReturnOrderRepositoryInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderSearchResultInterface;

interface ReturnOrderRepositoryInterface
{
    /**
     * @param int $id
     * @return ReturnOrderInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): ReturnOrderInterface;


    /**
     * @param string $incrementId
     * @return ReturnOrderInterface
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $incrementId): ReturnOrderInterface;


    /**
     * @param ReturnOrderInterface $order
     * @return ReturnOrderInterface
     */
    public function save(ReturnOrderInterface $order): ReturnOrderInterface;

    /**
     * @param ReturnOrderInterface $order
     * @return void
     */
    public function delete(ReturnOrderInterface $order): ReturnOrderInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReturnOrderSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReturnOrderSearchResultInterface;
}
