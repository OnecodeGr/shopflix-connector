<?php
/**
 * ItemRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ItemInterface;
use Onecode\ShopFlixConnector\Api\Data\ItemSearchResultInterface;

interface ItemRepositoryInterface
{
    /**
     * @param int $id
     * @return ItemInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id);


    /**
     * @param ItemInterface $orderItem
     * @return ItemInterface
     */
    public function save(ItemInterface $orderItem);

    /**
     * @param ItemInterface $orderItem
     * @return void
     */
    public function delete(ItemInterface $orderItem);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ItemSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
