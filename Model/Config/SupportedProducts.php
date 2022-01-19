<?php
/**
 * SupportedProducts.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Config;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SupportedProducts
 * @package Onecode\ShopFlixConnector\Model\Config
 */
class SupportedProducts implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => Product\Type::DEFAULT_TYPE, "label" => __(Product\Type::DEFAULT_TYPE)],
            ['value' => Configurable::TYPE_CODE, "label" => __(Configurable::TYPE_CODE)],
        ];
    }

}
