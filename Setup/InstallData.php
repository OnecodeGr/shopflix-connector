<?php
/**
 * InstallData.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Zend_Validate_Exception;

class InstallData implements InstallDataInterface
{

    /**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, CollectionFactory $collectionFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @throws Zend_Validate_Exception
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            Product::ENTITY,
            'onecode_shopflix_shipping_lead_time',
            [
                'group' => 'Onecode ShopFlix',
                'type' => 'int',
                'label' => 'Shipping Lead Time',
                'input' => 'select',
                'source' => 'Onecode\ShopFlixConnector\Model\Attribute\Source\ShippingLeadTime',
                'default' => '0',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => false
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'onecode_shopflix_sell_on',
            [
                'group' => 'ShopFlix',
                'type' => 'int',
                'label' => 'Sell On ShopFlix',
                'input' => 'boolean',
                'source' => "Magento\Eav\Model\Entity\Attribute\Source\Boolean",
                'default' => '1',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => false
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'onecode_shopflix_offer_date_from',
            [
                'group' => 'ShopFlix',
                'type' => 'datetime',
                'label' => 'Offer Date From',
                'input' => 'date',
                'frontend' => '',
                'source' => '',
                'default' => '',
                'backend' => 'Magento\Catalog\Model\Attribute\Backend\Startdate',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => false
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'onecode_shopflix_offer_date_to',
            [
                'group' => 'ShopFlix',
                'type' => 'datetime',
                'label' => 'Offer Date To',
                'input' => 'date',
                'frontend' => '',
                'source' => '',
                'default' => '',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => false
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'onecode_shopflix_offer_price',
            [
                'group' => 'ShopFlix',
                'type' => 'decimal',
                'label' => 'Offer Price',
                'input' => 'price',
                'source' => '',
                'default' => '',
                'frontend' => '',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => false
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'onecode_shopflix_offer_qty',
            [
                'group' => 'ShopFlix',
                'type' => 'int',
                'label' => 'Offer Qty',
                'input' => 'text',
                'source' => '',
                'default' => '',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => false
            ]
        );

        $products = $this->collectionFactory->create();
        $products->addAttributeToSelect("sku")->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
        /* @var Product $product */
        foreach ($products as $product) {
            $product->addAttributeUpdate("onecode_shopflix_sell_on", 1, 0);
            $product->addAttributeUpdate("onecode_shopflix_shipping_lead_time", 0, 0);
        }
    }

}
