<?php
/**
 * ExportProductData.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class ExportProductData
{
    private $_collectionFactory;
    private $_productVisibility;
    private $_productStatus;
    private $_helper;
    private $_storeManager;
    private $_localeResolver;
    private $_emulation;
    private $_categoryRepository;
    private $isInventoryEnable = true;
    private $_salableQtyDataBySku;
    private $_stockState;
    private $_simpler = false;

    /**
     * GenerateJson constructor.
     * @param CollectionFactory $productsCollectionFactory
     * @param Visibility $productVisibility
     * @param Status $productStatus
     * @param Data $data
     * @param StoreManagerInterface $storeManager
     * @param Resolver $localeResolver
     * @param Emulation $emulation
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        CollectionFactory     $productsCollectionFactory,
        Visibility            $productVisibility,
        Status                $productStatus,
        Data                  $data,
        StoreManagerInterface $storeManager,
        Resolver              $localeResolver,
        Emulation             $emulation,
        CategoryRepository    $categoryRepository
    )
    {
        $this->_collectionFactory = $productsCollectionFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_helper = $data;
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_emulation = $emulation;
        $this->_categoryRepository = $categoryRepository;
        $moduleManager = ObjectManager::getInstance()->create(Manager::class);
        if ($moduleManager->isEnabled("Magento_Inventory")) {
            $this->_salableQtyDataBySku = ObjectManager::getInstance()->create(GetSalableQuantityDataBySku::class);
        } else {
            $this->_stockState = ObjectManager::getInstance()->create(StockStateInterface::class);
            $this->isInventoryEnable = false;
        }
    }


    /**
     * @throws LocalizedException
     */
    public function exportData($storeId = null): array
    {
        if ($storeId) {
            $this->_localeResolver->emulate($storeId);
            $this->_emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        }
        /** @var Store $store */
        $store = $this->_storeManager->getStore();
        $select = [
            $this->_helper->getMpnAttribute(),
            $this->_helper->getTitleAttribute(),
            $this->_helper->getEanAttribute(),
            "image",
            "tax_class_id",
            "onecode_shopflix_shipping_lead_time",
            "onecode_shopflix_sell_on",
            "onecode_shopflix_offer_date_from",
            "onecode_shopflix_offer_date_to",
            "onecode_shopflix_offer_price",
            "onecode_shopflix_offer_qty",
        ];
        if (!$this->_simpler) {
            $select = array_merge($select, [
                $this->_helper->getWeightAttribute(),
                $this->_helper->getDescriptionAttribute(),
                $this->_helper->getManufacturerAttribute(),
            ]);
        }

        $collection = $this->_collectionFactory->create()
            ->addAttributeToSelect($select)
            ->addFieldToFilter('status', ['in' => $this->_productStatus->getVisibleStatusIds()])
            ->setVisibility($this->_productVisibility->getVisibleInSiteIds())->addTaxPercents()
            ->addFinalPrice()
            ->addFieldToFilter("onecode_shopflix_sell_on", true)
            ->addFieldToFilter("type_id", $this->_helper->getSelectedProductsTypes())
            ->addMediaGalleryData();

        $date = ObjectManager::getInstance()->get(
            DateTime::class
        )->date('Y-m-d H:i:s');
        $result = [
            'meta' => [
                "last_updated_at" => time(),
                "store_code" => $store->getCode(),
                "store_name" => $store->getFrontendName(),
                "locale" => $this->_localeResolver->getLocale(),
                "count" => $collection->count()
            ],
            "created_at" => $date,
            "products" => []
        ];

        /**
         * @var ProductInterface|Product $product
         */
        foreach ($collection as $product) {
            switch ($product->getTypeId()) {
                case Product\Type::DEFAULT_TYPE:
                    $this->processSimple($product, $result['products']);
                    break;
                case Configurable::TYPE_CODE:
                    /** @var ConfigurableType $typeInstance */
                        $typeInstance = $product->getTypeInstance();
                    $_children = $typeInstance->getUsedProducts($product);
                    /** @var Product $child */
                    foreach ($_children as $child) {
                        $this->processSimple($child, $result['products'], [], $product);
                    }
                    break;
                default:
                    break;
            }
        }

        $result['products'] = array_values($result['products']);

        if ($storeId) {
            $this->_emulation->stopEnvironmentEmulation();
        }
        return $result;
    }

    /**
     * @param Product|ProductInterface $product
     * @param $data
     * @param array $variation
     * @param Product|ProductInterface|null $parent
     * @return void
     */
    private function processSimple($product, &$data, array $variation = [], $parent = null): void
    {
        $stock = 0;
        if ($this->isInventoryEnable) {
            $sources = $this->_salableQtyDataBySku->execute($product->getSku());
            foreach ($sources as $source) {
                $stock += $source['qty'];
            }
        } else {
            $stock = $this->_stockState->getStockQty($product->getId());
        }
        try {
            $image = $product->getMediaGalleryImages()->getFirstItem()->getData("url");
            if (!$image && $parent) {
                $image = $parent->getMediaGalleryImages()->getFirstItem()->getData("url");
            }
        } catch (Exception $e) {
            $image = "";
        }

        $data[$product->getId()] = [
            "sku" => $product->getSku(),
            "mpn" => $product->getData($this->_helper->getMpnAttribute()),
            "ean" => $product->getData($this->_helper->getEanAttribute()),
            "name" => $product->getData($this->_helper->getTitleAttribute()),
            "price" => $parent != null ? $parent->getData("final_price") : number_format($product->getData('final_price'), 2),
            "list_price" => $parent != null ? $parent->getData("price") : number_format($product->getData('price'), 2),
            "url" => $parent != null ? $parent->getProductUrl() : $product->getProductUrl(),
            "shipping_lead_time" => $parent != null ? $parent->getData("onecode_shopflix_shipping_lead_time") : $product->getData("onecode_shopflix_shipping_lead_time"),
            "offer_from" => $parent != null ? $parent->getData("onecode_shopflix_offer_date_from") : $product->getData("onecode_shopflix_offer_date_from"),
            "offer_to" => $parent != null ? $parent->getData("onecode_shopflix_offer_date_to") : $product->getData("onecode_shopflix_offer_date_to"),
            "offer_price" => number_format($parent != null ? $parent->getData("onecode_shopflix_offer_price") : $product->getData("onecode_shopflix_offer_price"), 2),
            "offer_quantity" => $parent != null ? $parent->getData("onecode_shopflix_offer_qty") : $product->getData("onecode_shopflix_offer_qty"),
            "quantity" => $stock,
            "image" => $image,
        ];

        if (!$this->_simpler) {
            $data[$product->getId()] = array_merge($data[$product->getId()], [
                "description" => $product->getData($this->_helper->getDescriptionAttribute()),
                "weight" => number_format($product->getData($this->_helper->getWeightAttribute()), 2),
                "manufacturer" => $product->getAttributeText($this->_helper->getManufacturerAttribute()),
            ]);
            if ($this->_helper->exportCategory()) {
                $this->addCategories($product, $data[$product->getId()]);
            }
        }


    }

    /**
     * @param Product $product
     * @param $data
     */
    private function addCategories(Product $product, &$data)
    {
        $categories = [];
        $categoryNames = [];
        foreach ($product->getCategoryIds() as $categoryId) {
            if ($categoryId <= 2) {
                continue;
            }
            try {
                /* @var Category $category */
                $category = $this->_categoryRepository->get($categoryId);
            } catch (NoSuchEntityException $e) {
                continue;
            }
            $this->getCategoryTree($category, $categoryNames);
        }
        asort($categoryNames);
        foreach ($categoryNames as $category) {
            $categories [$category['id']] = $category['name'];
        }
        $data['category'] = implode(">", $categories);
    }

    /**
     * @param Category $category
     * @param $data
     */
    private function getCategoryTree(Category $category, &$data)
    {
        if ($category->getParentCategory()->getLevel() > 1) {
            $data[$category->getLevel()] = [
                "id" => $category->getId(),
                "name" => $category->getName()
            ];
            $this->getCategoryTree($category->getParentCategory(), $data);
        }

        $data[$category->getLevel()] = [
            "id" => $category->getId(),
            "name" => $category->getName()
        ];
    }

    /**
     * Set Simpler
     * @param bool $simpler
     * @return $this
     */
    public function isSimpler(bool $simpler): ExportProductData
    {
        $this->_simpler = $simpler;
        return $this;
    }
}
