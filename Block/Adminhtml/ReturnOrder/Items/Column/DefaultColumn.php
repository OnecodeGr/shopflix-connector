<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\Column;

use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\AbstractItems;

class DefaultColumn extends AbstractItems
{

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku(): string
    {
        return $this->getItem()->getSku();
    }

    /**
     * Get item
     *
     * @return ReturnOrderItemInterface
     */
    public function getItem(): ReturnOrderItemInterface
    {
        $item = $this->_getData('item');
        if ($item instanceof ReturnOrderItemInterface) {
            return $item;
        } else {
            return $item->getOrderItem();
        }
    }

    /**
     * Calculate total amount for the item
     *
     * @param ReturnOrderItemInterface $item
     * @return float|int
     */
    public function getTotalAmount(ReturnOrderItemInterface $item)
    {
        return $item->getRowTotalPrice();
    }

    /**
     * Calculate total amount for the item
     *
     * @param ReturnOrderItemInterface $item
     * @return float
     */
    public function getBaseTotalAmount(ReturnOrderItemInterface $item): float
    {
        return $item->getPrice();
    }


}
