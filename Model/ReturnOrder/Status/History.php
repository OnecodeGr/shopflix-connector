<?php
/**
 * History.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder\Status;

use Magento\Framework\Model\AbstractModel;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistoryInterface as StatusHistoryInterface;
use Onecode\ShopFlixConnector\Model\ReturnOrder;

class History extends AbstractModel implements StatusHistoryInterface
{
    /**
     * Order instance
     *
     * @var ReturnOrder
     */
    protected $_order;

    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_status_history';

    /**
     * @var string
     */
    protected $_eventObject = 'status_history';

    /**
     * Retrieve status label
     *
     * @return string|null
     */
    public function getStatusLabel()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStatusLabel();
        }
        return null;
    }

    /**
     * Retrieve order instance
     *
     * @codeCoverageIgnore
     *
     * @return ReturnOrder
     */
    public function getOrder(): ReturnOrder
    {
        return $this->_order;
    }

    /**
     * Set order object and grab some metadata from it
     *
     * @param ReturnOrder $order
     * @return $this
     */
    public function setOrder(ReturnOrder $order): History
    {
        $this->_order = $order;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->getData(StatusHistoryInterface::STATUS);
    }

    public function beforeSave(): History
    {
        parent::beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->getData(StatusHistoryInterface::PARENT_ID);
    }

    public function setParentId(int $id): StatusHistoryInterface
    {
        return $this->setData(StatusHistoryInterface::PARENT_ID, $id);
    }

    /**
     * Returns comment
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->getData(StatusHistoryInterface::COMMENT);
    }

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData(StatusHistoryInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt): StatusHistoryInterface
    {
        return $this->setData(StatusHistoryInterface::CREATED_AT, $createdAt);
    }

    public function setComment(string $comment): StatusHistoryInterface
    {
        return $this->setData(StatusHistoryInterface::COMMENT, $comment);
    }

    public function setStatus(string $status): StatusHistoryInterface
    {
        return $this->setData(StatusHistoryInterface::STATUS, $status);
    }

    public function getIsShopFlix(): ?bool
    {
        return $this->_getData(StatusHistoryInterface::IS_SHOPFLIX);
    }

    public function setIsShopFlix(bool $isShopFlix): StatusHistoryInterface
    {
        return $this->setData(StatusHistoryInterface::IS_SHOPFLIX, $isShopFlix);
    }

    protected function _construct()
    {
        $this->setIdFieldName(StatusHistoryInterface::ENTITY_ID);
        $this->_init(\Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Status\History::class);
    }
}
