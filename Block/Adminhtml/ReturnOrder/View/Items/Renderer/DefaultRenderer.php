<?php
/**
 * DefaultRenderer.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\View\Items\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;


class DefaultRenderer extends \Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\Renderer\DefaultRenderer
{

    /**
     * Retrieve real html id for field
     *
     * @param string $id
     * @return string
     */
    public function getFieldId(string $id): string
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * Retrieve field html id prefix
     *
     * @return string
     */
    public function getFieldIdPrefix(): string
    {
        return 'shopflix_return_order_item_' . $this->getItem()->getId() . '_';
    }

    /**
     * Get order item
     *
     * @return ReturnOrderItemInterface
     */
    public function getItem(): ReturnOrderItemInterface
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve default value for giftmessage sender
     *
     * @return string
     */
    public function getDefaultSender()
    {
        if (!$this->getItem()) {
            return '';
        }

        if ($this->getItem()->getOrder()) {
            return $this->getItem()->getOrder()->getBillingAddress()->getName();
        }

        return $this->getItem()->getBillingAddress()->getName();
    }

    /**
     * Retrieve default value for giftmessage recipient
     *
     * @return string
     */
    public function getDefaultRecipient()
    {
        if (!$this->getItem()) {
            return '';
        }

        if ($this->getItem()->getOrder()) {
            if ($this->getItem()->getOrder()->getShippingAddress()) {
                return $this->getItem()->getOrder()->getShippingAddress()->getName();
            } elseif ($this->getItem()->getOrder()->getBillingAddress()) {
                return $this->getItem()->getOrder()->getBillingAddress()->getName();
            }
        }

        if ($this->getItem()->getShippingAddress()) {
            return $this->getItem()->getShippingAddress()->getName();
        } elseif ($this->getItem()->getBillingAddress()) {
            return $this->getItem()->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Retrieve rendered column html content
     *
     * @param DataObject|ReturnOrderItemInterface $item
     * @param string $column
     * @param string|null $field
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws LocalizedException
     * @since 100.1.0
     */
    public function getColumnHtml(DataObject $item, string $column, string $field = null): string
    {
        $html = '';
        switch ($column) {
            case 'product':
                if ($this->canDisplayContainer()) {
                    $html .= '<div id="' . $this->getHtmlId() . '">';
                }
                $html .= $this->getColumnHtml($item, 'name');
                if ($this->canDisplayContainer()) {
                    $html .= '</div>';
                }
                break;
            case 'total':
            case 'price':
                $html = $this->displayPriceAttribute('price');
                break;
            default:
                $html = parent::getColumnHtml($item, $column, $field);
        }
        return $html;
    }

    /**
     * Indicate that block can display container
     *
     * @return bool
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    public function canDisplayContainer(): bool
    {
        return $this->getRequest()->getParam('reload') != 1;
    }

    /**
     * Retrieve block html id
     *
     * @return string
     */
    public function getHtmlId(): string
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Get columns data.
     *
     * @return array
     * @since 100.1.0
     */
    public function getColumns()
    {
        return array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
    }
}
