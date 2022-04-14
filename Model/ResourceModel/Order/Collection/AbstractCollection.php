<?php
/**
 * AbstractCollection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Collection;

use Onecode\ShopFlixConnector\Model\Order;

abstract class AbstractCollection extends
    \Onecode\ShopFlixConnector\Model\ResourceModel\Collection\AbstractCollection
{

    /**
     * Order object
     *
     * @var Order
     */
    protected $_shopflixOrder = null;

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField = 'parent_id';

    /**
     * Retrieve sales order as parent collection object
     *
     * @return Order|null
     */
    public function getShopflixOrder(): ?Order
    {
        return $this->_shopflixOrder;
    }

    public function setShopflixOrder($order): AbstractCollection
    {
        $this->_shopflixOrder = $order;
        if ($this->_eventPrefix && $this->_eventObject) {
            $this->_eventManager->dispatch(
                $this->_eventPrefix . '_set_shopflix_order',
                ['collection' => $this, $this->_eventObject => $this, 'order' => $order]
            );
        }

        return $this;
    }

    /**
     * Add order filter
     *
     * @param int|Order|array $order
     * @return \Onecode\ShopFlixConnector\Model\ResourceModel\Collection\AbstractCollection
     */
    public function setOrderFilter($order): \Onecode\ShopFlixConnector\Model\ResourceModel\Collection\AbstractCollection
    {
        if ($order instanceof Order) {
            $this->setShopflixOrder($order);
            $orderId = $order->getId();
            if ($orderId) {
                $this->addFieldToFilter($this->_orderField, $orderId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->_orderField, $order);
        }
        return $this;
    }

}
