<?php
/**
 * History.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\Order\View\Tab;

use DateTime;
use DateTimeInterface;
use IntlDateFormatter;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Helper\Admin;
use Onecode\ShopFlixConnector\Model\Order;

class History extends Template implements TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Onecode_ShopFlixConnector::order/view/tab/history.phtml';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Admin
     */
    private $adminHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        Context  $context,
        Registry $registry,
        Admin    $adminHelper,
        array    $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->adminHelper = $adminHelper;
    }

    /**
     * Comparison For Sorting History By Timestamp
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    public static function sortHistoryByTimestamp($a, $b)
    {
        $createdAtA = $a['created_at'];
        $createdAtB = $b['created_at'];

        return $createdAtA->getTimestamp() <=> $createdAtB->getTimestamp();
    }

    /**
     * @return array
     */
    public function getFullHistory()
    {
        $order = $this->getOrder();

        $history = [];
        foreach ($order->getAllStatusHistory() as $orderComment) {
            $history[] = $this->_prepareHistoryItem(
                $orderComment->getStatusLabel(),
                $orderComment->getIsCustomerNotified(),
                $this->getOrderAdminDate($orderComment->getCreatedAt()),
                $orderComment->getComment()
            );
        }


        usort($history, [__CLASS__, 'sortHistoryByTimestamp']);
        return $history;
    }

    /**
     * Retrieve order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_shopflix_order');
    }

    /**
     * Map history items as array
     *
     * @param string $label
     * @param bool $notified
     * @param DateTimeInterface $created
     * @param string $comment
     * @return array
     */
    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return ['title' => $label, 'notified' => $notified, 'comment' => $comment, 'created_at' => $created];
    }

    /**
     * Get order admin date
     *
     * @param int $createdAt
     * @return DateTime
     */
    public function getOrderAdminDate($createdAt)
    {
        return $this->_localeDate->date(new DateTime($createdAt));
    }

    /**
     * Status history date/datetime getter
     *
     * @param array $item
     * @param string $dateType
     * @param int $format
     * @return string
     */
    public function getItemCreatedAt(array $item, $dateType = 'date', $format = IntlDateFormatter::MEDIUM)
    {
        if (!isset($item['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->formatDate($item['created_at'], $format);
        }
        return $this->formatTime($item['created_at'], $format);
    }

    /**
     * Status history item title getter
     *
     * @param array $item
     * @return string
     */
    public function getItemTitle(array $item)
    {
        return isset($item['title']) ? $this->escapeHtml($item['title']) : '';
    }

    /**
     * Check whether status history comment is with customer notification
     *
     * @param array $item
     * @param bool $isSimpleCheck
     * @return bool
     */
    public function isItemNotified(array $item, $isSimpleCheck = true)
    {
        if ($isSimpleCheck) {
            return !empty($item['notified']);
        }
        return isset($item['notified']) && false !== $item['notified'];
    }

    /**
     * Status history item comment getter
     *
     * @param array $item
     * @return string
     */
    public function getItemComment(array $item)
    {
        $allowedTags = ['b', 'br', 'strong', 'i', 'u', 'a'];
        return isset($item['comment'])
            ? $this->adminHelper->escapeHtmlWithLinks($item['comment'], $allowedTags) : '';
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Comments History');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Order History');
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('shopflix/*/commentsHistory', ['_current' => true]);
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }

}
