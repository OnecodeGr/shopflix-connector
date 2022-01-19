<?php
/**
 * Form.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\Reject\Create;

use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Block\Adminhtml\Order\AbstractOrder;


class Form extends AbstractOrder
{
    /**
     * Retrieve source
     *
     * @return OrderInterface
     * @throws LocalizedException
     */
    public function getSource(): OrderInterface
    {
        return $this->getOrder();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getSaveUrl(): string
    {
        return $this->getUrl('shopflix/*/save', ['order_id' => $this->getOrder()->getId()]);
    }
}
