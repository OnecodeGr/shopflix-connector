<?php
/**
 * Items.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View;

use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Block\Adminhtml\Items\AbstractItems;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item\Collection;

class Items extends AbstractItems
{
    /**
     * @return array
     * @since 100.1.0
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }

    /**
     * Retrieve order items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new LocalizedException(__('Invalid parent block for this block'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        parent::_beforeToHtml();
    }
}
