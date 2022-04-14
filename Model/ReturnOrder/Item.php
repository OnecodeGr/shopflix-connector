<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Module\Manager;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderAddressInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Item as ResourceModel;
use Onecode\ShopFlixConnector\Model\ReturnOrder as OrderModel;
use Onecode\ShopFlixConnector\Model\ReturnOrderRepository;

class Item extends AbstractModel implements ReturnOrderItemInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_item_model';
    /**
     * @var OrderModel
     */
    private $_order;
    private $_orderRepository;
    private $isInventoryEnable;
    private $_salableQtyDataBySku;
    private $_productRepository;

    public function getItemId(): ?int
    {
        return $this->getData(ReturnOrderItemInterface::ITEM_ID);
    }

    public function setItemId(int $id): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::ITEM_ID, $id);
    }

    public function getRowTotalPrice()
    {
        return $this->getPrice() * $this->getQty();
    }
    public function getParentItemId(): int
    {
        return $this->getData(ReturnOrderItemInterface::PARENT_ITEM_ID);
    }

    public function setParentItemId(int $parentItemId): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::PARENT_ITEM_ID, $parentItemId);
    }

    public function getProductId(): int
    {
        return $this->getData(ReturnOrderItemInterface::PRODUCT_ID);
    }

    public function setProductId(int $productId): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::PRODUCT_ID, $productId);
    }

    public function getProductType(): string
    {
        return $this->getData(ReturnOrderItemInterface::PRODUCT_TYPE);
    }

    public function setProductType(string $productType): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::PRODUCT_TYPE, $productType);
    }

    public function getSku(): string
    {
        return $this->getData(ReturnOrderItemInterface::SKU);
    }

    public function setSku(string $sku): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::SKU, $sku);
    }

    public function getPrice(): float
    {
        return $this->getData(ReturnOrderItemInterface::PRICE);
    }

    public function setPrice(float $price): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::PRICE, $price);
    }

    public function getQty(): int
    {
        return $this->getData(ReturnOrderItemInterface::QTY);
    }

    public function setQty(int $qty): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::QTY, $qty);
    }

    public function getName(): string
    {
        return $this->getData(ReturnOrderItemInterface::NAME);
    }

    public function setName(string $name): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::NAME, $name);
    }

    public function getReturnReason(): string
    {
        return $this->getData(ReturnOrderItemInterface::RETURN_REASON);
    }

    public function setReturnReason(string $returnReason): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::RETURN_REASON, $returnReason);
    }

    /**
     * Retrieve order model object
     *
     * @return OrderModel
     * @throws NoSuchEntityException
     */
    public function getOrder(): OrderModel
    {
        if ($this->_order === null && ($orderId = $this->getOrderId())) {
            $order = $this->_orderRepository->getById($orderId);
            $this->setOrder($order);
        }

        return $this->_order;
    }

    public function getOrderId(): int
    {
        return $this->getData(ReturnOrderItemInterface::ORDER_ID);
    }

    /**
     * Declare order
     *
     * @param OrderModel $order
     *
     * @return $this
     */
    public function setOrder(OrderModel $order): ReturnorderItemInterface
    {
        $this->_order = $order;
        $this->setOrderId($order->getId());

        return $this;
    }

    public function setOrderId(int $orderId): ReturnOrderItemInterface
    {
        return $this->setData(ReturnOrderItemInterface::ORDER_ID, $orderId);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getRealQty(): array
    {
        if ($this->isInventoryEnable) {
            return $this->_salableQtyDataBySku->execute($this->getSku());
        }

        return [
            [
                "stock_name" => __("Default"),
                "qty" => $this->getProduct()->getExtensionAttributes()->getStockItem()->getQty()
            ]
        ];
    }

    /**
     * @throws NoSuchEntityException
     */
    public function isOrderedQtyLower(): bool
    {

        $realQty = 0;
        foreach ( $this->getRealQty() as $realQtyData){
            $realQty += (int)$realQtyData['qty'];
        }
        return $this->getQty() < $realQty;
    }


    /**
     * @inheridoc
     * @throws NoSuchEntityException
     */
    public function getProduct()
    {
        return $this->_productRepository->getById($this->getProductId());
    }
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setIdFieldName(ReturnOrderItemInterface::ITEM_ID);
        $this->_init(ResourceModel::class);

        $this->_orderRepository = ObjectManager::getInstance()->create(ReturnOrderRepository::class);
        $this->_productRepository = ObjectManager::getInstance()->create(ProductRepository::class);
        $moduleManager = ObjectManager::getInstance()->create(Manager::class);
        if ($moduleManager->isEnabled("Magento_Inventory")) {
            $this->_salableQtyDataBySku = ObjectManager::getInstance()->create(GetSalableQuantityDataBySku::class);
        } else {
            $this->isInventoryEnable = false;
        }
    }


    /**
     * @return ReturnOrderAddressInterface|null
     * @throws NoSuchEntityException
     */
    public function getBillingAddress(): ?ReturnOrderAddressInterface
    {
        return $this->getOrder()->getBillingAddress();
    }


    public function getShippingAddress(): ?ReturnOrderAddressInterface
    {
        return $this->getOrder()->getShippingAddress();
    }
}
