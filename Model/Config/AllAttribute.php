<?php
/**
 * AllAttribute.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Config;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\OptionSourceInterface;

class AllAttribute implements OptionSourceInterface
{

    /** @var  CollectionFactory */
    protected $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        foreach ($this->_getOptions() as $optionValue => $optionLabel) {
            $options[] = ['value' => $optionValue, 'label' => $optionLabel];
        }
        return $options;
    }

    protected function _getOptions(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addOrder('attribute_code', 'asc');
        $options = [];

        foreach ($collection->getItems() as $attribute) {
            /** @var Attribute $attribute */
            $options[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->_getOptions();
    }


}
