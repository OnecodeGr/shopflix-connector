<?php
/**
 * ConfigProvider.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Gateway\Config;

use Magento\Payment\Gateway\Config\Config;


class ConfigProvider extends Config
{
    const CODE = 'onecode_shopflix_payment';
    const KEY_ACTIVE = 'active';
    const KEY_ORDER_STATUS = "order_status";
    const KEY_INSTRUCTIONS = "instructions";
    const KEY_TITLE = "title";

    public function isActive($storeId = null): bool
    {
        return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
    }

    public function getMethodDefaultStatus($storeId = null): string
    {
        return $this->getValue(self::KEY_ORDER_STATUS, $storeId);
    }

    public function getConfig(): array
    {

        return [
            'payment' => [
                self::CODE => [
                    "instructions" => $this->getMethodInstructions(),
                    "title" => __("SHOPFLIX Payment Method")
                ]
            ]
        ];

    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getMethodInstructions($storeId = null): string
    {
        return $this->getValue(self::KEY_INSTRUCTIONS, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getMethodTitle($storeId = null): string
    {
        return $this->getValue(self::KEY_TITLE, $storeId);
    }
}
