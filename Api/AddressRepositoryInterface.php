<?php
/**
 * AddressRepositoryInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Onecode\ShopFlixConnector\Api\Data\AddressInterface;
use Onecode\ShopFlixConnector\Api\Data\AddressSearchResultInterface;

interface AddressRepositoryInterface
{
    /**
     * @param int $id
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id);


    /**
     * @param AddressInterface $orderAddress
     * @return AddressRepositoryInterface
     */
    public function save(AddressInterface $orderAddress);

    /**
     * @param AddressInterface $orderAddress
     * @return void
     */
    public function delete(AddressInterface $orderAddress);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return AddressSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
