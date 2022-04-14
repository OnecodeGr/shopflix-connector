<?php
/**
 * AbstractItems.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Items;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Onecode\ShopFlixConnector\Model\Order;
use RuntimeException;

/**
 * Abstract items renderer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractItems extends Template
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Renderers for other column with column name key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_columnRenders = [];

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context                     $context,
        StockRegistryInterface      $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry                    $registry,
        array                       $data = []
    )
    {
        $this->stockRegistry = $stockRegistry;
        $this->stockConfiguration = $stockConfiguration;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve rendered item html content
     *
     * @param DataObject $item
     * @return string
     */
    public function getItemHtml(DataObject $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }

        return $this->getItemRenderer($type)->setItem($item)->setCanEditQty(false)->toHtml();
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return AbstractBlock
     * @throws RuntimeException|LocalizedException
     */
    public function getItemRenderer(string $type)
    {
        /** @var $renderer AbstractItems */
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (!$renderer instanceof BlockInterface) {
            throw new RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setColumnRenders($this->getLayout()->getGroupChildNames($this->getNameInLayout(), 'column'));

        return $renderer;
    }

    /**
     * Add column renderers
     *
     * @param array $blocks
     * @return $this
     * @throws LocalizedException
     */
    public function setColumnRenders(array $blocks)
    {
        foreach ($blocks as $blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block->getRenderedBlock() === null) {
                $block->setRenderedBlock($this);
            }
            $this->_columnRenders[$blockName] = $block;
        }
        return $this;
    }


    /**
     * Retrieve rendered item extra info html content
     *
     * @param DataObject $item
     * @return string
     */
    public function getItemExtraInfoHtml(DataObject $item)
    {
        $extraInfoBlock = $this->getChildBlock('order_item_extra_info');
        if ($extraInfoBlock) {
            return $extraInfoBlock->setItem($item)->toHtml();
        }
        return '';
    }

    /**
     * Retrieve rendered column html content
     *
     * @param DataObject $item
     * @param string $column the column key
     * @param string $field the custom item field
     * @return string
     */
    public function getColumnHtml(DataObject $item, $column, $field = null)
    {
        if ($item->getOrderItem()) {
            $block = $this->getColumnRenderer($column, $item->getOrderItem()->getProductType());
        } else {
            $block = $this->getColumnRenderer($column, $item->getProductType());
        }

        if ($block) {
            $block->setItem($item);
            if ($field !== null) {
                $block->setField($field);
            }
            return $block->toHtml();
        }
        return '&nbsp;';
    }

    /**
     * Retrieve column renderer block
     *
     * @param string $column
     * @param string $compositePart
     * @return AbstractBlock
     */
    public function getColumnRenderer(string $column, string $compositePart = '')
    {
        $column = 'column_' . $column;
        if (isset($this->_columnRenders[$column . '_' . $compositePart])) {
            $column .= '_' . $compositePart;
        }
        if (!isset($this->_columnRenders[$column])) {
            return false;
        }
        return $this->_columnRenders[$column];
    }

    /**
     * Retrieve price attribute html content
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        return $this->displayPrices(
            $this->getPriceDataObject()->getData($code),
            $strong,
            $separator
        );

    }

    /**
     * Retrieve price formatted html content
     *
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPrices($price, $strong = false, $separator = '<br />')
    {
        return $this->displayRoundedPrices($price, 2, $strong, $separator);
    }

    /**
     * Display base and regular prices with specified rounding precision
     *
     * @param float $price
     * @param int $precision
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayRoundedPrices($price, $precision = 2, $strong = false, $separator = '<br />')
    {

        $res = $this->getOrder()->formatPricePrecision($price, $precision);
        if ($strong) {
            $res = '<strong>' . $res . '</strong>';
        }

        return $res;
    }

    /**
     * Retrieve available order
     *
     * @return Order
     * @throws LocalizedException
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if ($this->_coreRegistry->registry('current_shopflix_order')) {
            return $this->_coreRegistry->registry('current_shopflix_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }

        if ($this->getItem()->getOrder()) {
            return $this->getItem()->getOrder();
        }

        throw new LocalizedException(__('We can\'t get the order instance right now.'));
    }

    /**
     * Retrieve price data object
     *
     * @return Order
     * @throws LocalizedException
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getOrder();
        }
        return $obj;
    }

    public function formatPrice($price)
    {
        return $this->getOrder()->formatPrice($price);
    }
}
