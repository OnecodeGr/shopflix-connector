<?php
/**
 * DefaultRenderer.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Items\Renderer;

use Onecode\ShopFlixConnector\Block\Adminhtml\Items\AbstractItems;
use Onecode\ShopFlixConnector\Model\Order\Item;

class DefaultRenderer extends AbstractItems
{
    /**
     * Get order item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->_getData('item');
    }
}
