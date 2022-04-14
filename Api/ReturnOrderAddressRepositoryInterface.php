<?php
/**
 * ReturnAddressRepositoryInterface.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderAddressInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderAddressSearchResultInterface;

interface ReturnOrderAddressRepositoryInterface
{
    /**
     * @param int $id
     * @return ReturnOrderAddressInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id);


    /**
     * @param ReturnOrderAddressInterface $orderAddress
     * @return AddressRepositoryInterface
     */
    public function save(ReturnOrderAddressInterface $orderAddress);

    /**
     * @param ReturnOrderAddressInterface $orderAddress
     * @return void
     */
    public function delete(ReturnOrderAddressInterface $orderAddress);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReturnOrderAddressSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
