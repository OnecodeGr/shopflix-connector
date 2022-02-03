<?php
/**
 * Item.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Module\Manager;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Onecode\ShopFlixConnector\Api\Data\ItemInterface;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\Order as OrderModel;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item as ResourceModel;

class Item extends AbstractModel implements ItemInterface
{

    private $_order;
    private $_productRepository;
    private $_orderRepository;

    private $isInventoryEnable = true;

    /**
     * @var GetSalableQuantityDataBySku|mixed
     */
    private $_salebleQtyDataBySku;
    /**
     * @var Manager|mixed
     */
    private $_moduleManager;

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel::class);
        $this->setIdFieldName(ItemInterface::ITEM_ID);
        $this->_orderRepository = ObjectManager::getInstance()->create(OrderRepository::class);
        $this->_productRepository = ObjectManager::getInstance()->create(ProductRepository::class);
        $moduleManager = ObjectManager::getInstance()->create(Manager::class);
        if ($moduleManager->isEnabled("Magento_Inventory")) {
            $this->_salebleQtyDataBySku = ObjectManager::getInstance()->create(GetSalableQuantityDataBySku::class);
        } else {
            $this->isInventoryEnable = false;
        }

    }

    public function getItemId(): int
    {
        return $this->_getData(ItemInterface::ITEM_ID);
    }

    public function setItemId(int $id): ItemInterface
    {
        return $this->setData(ItemInterface::ITEM_ID, $id);
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

    /**
     * Declare order
     *
     * @param OrderModel $order
     *
     * @return $this
     */
    public function setOrder(OrderModel $order): ItemInterface
    {
        $this->_order = $order;
        $this->setOrderId($order->getId());

        return $this;
    }

    public function getOrderId(): int
    {
        return $this->_getData(ItemInterface::ORDER_ID);
    }

    public function setOrderId($orderId): ItemInterface
    {
        return $this->setData(ItemInterface::ORDER_ID, $orderId);
    }

    public function getProductType(): string
    {
        return $this->_getData(ItemInterface::PRODUCT_TYPE);
    }

    public function getParentItemId(): int
    {
        return $this->_getData(ItemInterface::PARENT_ITEM_ID);
    }

    public function getName(): string
    {
        return $this->_getData(ItemInterface::NAME);
    }

    public function getRowTotalPrice()
    {
        return $this->getPrice() * $this->getQty();
    }

    public function getPrice(): float
    {
        return $this->_getData(ItemInterface::PRICE);
    }

    public function getQty(): int
    {
        return $this->_getData(ItemInterface::QTY);
    }

    public function isOrderedQtyLower(): bool
    {

        $realQty = 0;
        foreach ( $this->getRealQty() as $realQtyData){
            $realQty += $realQtyData['qty'];
        }
        return $this->getQty() < $realQty;
    }

    public function getRealQty(): array
    {
        if ($this->isInventoryEnable) {
            return $this->_salebleQtyDataBySku->execute($this->getSku());
        }

        return [
            [
                "stock_name" => __("Default"),
                "qty" => $this->getProduct()->getExtensionAttributes()->getStockItem()->getQty()
            ]
        ];

    }

    public function getSku(): string
    {
        return $this->_getData(ItemInterface::SKU);
    }

    /**
     * @inheridoc
     * @throws NoSuchEntityException
     */
    public function getProduct()
    {
        return $this->_productRepository->getById($this->getProductId());
    }

    public function getProductId(): int
    {
        return $this->_getData(ItemInterface::PRODUCT_ID);
    }

    public function setParentItemId(int $parentItemId): ItemInterface
    {
        return $this->setData(ItemInterface::PARENT_ITEM_ID, $parentItemId);
    }

    public function setProductId(int $productId): ItemInterface
    {
        return $this->setData(ItemInterface::PRODUCT_ID, $productId);
    }

    public function setProductType(string $productType): ItemInterface
    {
        return $this->setData(ItemInterface::PRODUCT_TYPE, $productType);
    }

    public function setSku(string $sku): ItemInterface
    {
        return $this->setData(ItemInterface::SKU, $sku);
    }

    public function setPrice(float $price): ItemInterface
    {
        return $this->setData(ItemInterface::PRICE, $price);
    }

    public function setQty(int $qty): ItemInterface
    {
        return $this->setData(ItemInterface::QTY, $qty);
    }

    public function setName(string $name): ItemInterface
    {
        return $this->setData(ItemInterface::NAME, $name);

    }


}
