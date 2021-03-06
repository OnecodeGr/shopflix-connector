<?php
/**
 * Address.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Handler;

use Exception;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\ResourceModel\Attribute;

class Address
{

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @param Attribute $attribute
     */
    public function __construct(
        Attribute $attribute
    )
    {
        $this->attribute = $attribute;
    }

    /**
     * Remove empty addresses from order
     *
     * @param Order $order
     * @return $this
     */
    public function removeEmptyAddresses(Order $order)
    {
        if ($order->hasBillingAddressId() && $order->getBillingAddressId() === null) {
            $order->unsBillingAddressId();
        }

        if ($order->hasShippingAddressId() && $order->getShippingAddressId() === null) {
            $order->unsShippingAddressId();
        }
        return $this;
    }

    /**
     * Process addresses saving
     *
     * @param Order $order
     * @return $this
     * @throws Exception
     */
    public function process(Order $order)
    {
        if (null !== $order->getAddresses()) {
            /** @var Order\Address $address */
            foreach ($order->getAddresses() as $address) {
                $address->setParentId($order->getId());
                $address->setOrder($order);
                $address->save();
            }
            $billingAddress = $order->getBillingAddress();
            $attributesForSave = [];
            if ($billingAddress && $order->getBillingAddressId() != $billingAddress->getId()) {
                $order->setBillingAddressId($billingAddress->getId());
                $attributesForSave[] = 'billing_address_id';
            }
            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress && $order->getShippingAddressId() != $shippingAddress->getId()) {
                $order->setShippingAddressId($shippingAddress->getId());
                $attributesForSave[] = 'shipping_address_id';
            }
            if (!empty($attributesForSave)) {
                $this->attribute->saveAttribute($order, $attributesForSave);
            }
        }
        return $this;
    }
}
