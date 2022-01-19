<?php
/**
 * OrderRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderSearchResultInterface;

interface OrderRepositoryInterface
{
    /**
     * @param int $id
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id);


    /**
     * @param string $incrementId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $incrementId);


    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function save(OrderInterface $order);

    /**
     * @param OrderInterface $order
     * @return void
     */
    public function delete(OrderInterface $order);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return OrderSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
