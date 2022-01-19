<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2021 ${ORGANIZATION_NAME}  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Items\Column;

use Onecode\ShopFlixConnector\Block\Adminhtml\Items\AbstractItems;
use Onecode\ShopFlixConnector\Model\Order\Item;

class DefaultColumn extends AbstractItems
{

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getItem()->getSku();
    }

    /**
     * Get item
     *
     * @return Item
     */
    public function getItem()
    {
        $item = $this->_getData('item');
        if ($item instanceof Item) {
            return $item;
        } else {
            return $item->getOrderItem();
        }
    }

    /**
     * Calculate total amount for the item
     *
     * @param Item $item
     * @return mixed
     */
    public function getTotalAmount($item)
    {
        return $item->getRowTotalPrice();
    }

    /**
     * Calculate total amount for the item
     *
     * @param Item $item
     * @return mixed
     */
    public function getBaseTotalAmount($item)
    {
        return $item->getPrice();
    }


}
