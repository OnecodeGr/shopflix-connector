<?php
/**
 * AbstractCollection.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
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
    protected $_salesOrder = null;

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
    public function getSalesOrder()
    {
        return $this->_salesOrder;
    }

    public function setSalesOrder($order)
    {
        $this->_salesOrder = $order;
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
    public function setOrderFilter($order)
    {
        if ($order instanceof Order) {
            $this->setSalesOrder($order);
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
