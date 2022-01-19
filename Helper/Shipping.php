<?php
/**
 * Payment.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface as Scope;

class Shipping extends AbstractHelper
{


    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getShippingConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, Scope::SCOPE_STORE, $storeId);
    }
}
