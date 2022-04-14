<?php
/**
 * DefaultRenderer.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\Renderer;

use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\AbstractItems;

class DefaultRenderer extends AbstractItems
{
    /**
     * Get order item
     *
     * @return ReturnOrderItemInterface
     */
    public function getItem(): ReturnOrderItemInterface
    {
        return $this->_getData('item');
    }
}
