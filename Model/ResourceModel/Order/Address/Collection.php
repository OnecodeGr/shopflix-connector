<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Address;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order\Address;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Address as AddressResource;

class Collection extends AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order_address_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'onecode_shopflix_order_address_collection';
    /**
     * Order object
     *
     * @var Order
     */
    protected $_order = null;

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
    public function getOrder(): ?Order
    {
        return $this->_order;
    }

    /**
     * Set sales order model as parent collection object
     *
     * @param Order $order
     * @return $this
     */
    public function setShopflixOrder(Order $order): Collection
    {
        $this->_order = $order;

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
     * @return $this
     */
    public function setOrderFilter($order): Collection
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

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Address::class,
            AddressResource::class
        );
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', [$this->_eventObject => $this]);

        return $this;
    }
}
