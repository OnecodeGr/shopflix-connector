<?php
/**
 * AbstractCollection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Collection;

use Onecode\ShopFlixConnector\Model\ReturnOrder as Order;

abstract class AbstractCollection extends
    \Onecode\ShopFlixConnector\Model\ResourceModel\Collection\AbstractCollection
{

    /**
     * Order object
     *
     * @var Order
     */
    protected $_returnOrder = null;

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
    public function getReturnedOrder(): ?Order
    {
        return $this->_returnOrder;
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
            $this->setReturnedOrder($order);
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

    public function setReturnedOrder($order): AbstractCollection
    {
        $this->_returnOrder = $order;
        if ($this->_eventPrefix && $this->_eventObject) {
            $this->_eventManager->dispatch(
                $this->_eventPrefix . '_set_shopflix_return_order',
                ['collection' => $this, $this->_eventObject => $this, 'order' => $order]
            );
        }

        return $this;
    }

}
