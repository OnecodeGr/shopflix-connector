<?php
/**
 * Items.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View;

use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\Items\AbstractItems;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item\Collection;

/**
 * @method setOrder(OrderInterface $order)
 */
class Items extends AbstractItems
{
    public function getColumns(): array
    {
        return array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
    }

    /**
     * Retrieve order items collection
     *
     * @return Collection
     * @throws LocalizedException
     */
    public function getItemsCollection(): Collection
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
