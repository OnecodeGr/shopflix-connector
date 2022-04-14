<?php
/**
 * Track.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Shipment;

use Magento\Framework\Model\AbstractModel;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Track as ResourceModel;

class Track extends AbstractModel implements ShipmentTrackInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_shipment_track';
    /**
     * @var string
     */
    protected $_eventObject = 'shipment_track';

    /**
     * @inheritDoc
     */
    public function setOrderId($id)
    {
        return $this->setData(ShipmentTrackInterface::ORDER_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(ShipmentTrackInterface::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(ShipmentTrackInterface::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(ShipmentTrackInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return $this->getData(ShipmentTrackInterface::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(ShipmentTrackInterface::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($timestamp)
    {
        return $this->setData(ShipmentTrackInterface::UPDATED_AT, $timestamp);
    }

    /**
     * @inheritDoc
     */
    public function setParentId($id)
    {
        return $this->setData(ShipmentTrackInterface::PARENT_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setTrackNumber($trackNumber)
    {
        return $this->setData(ShipmentTrackInterface::TRACK_NUMBER, $trackNumber);
    }

    /**
     * @inheritDoc
     */
    public function getTrackNumber()
    {
        return $this->getData(ShipmentTrackInterface::TRACK_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setTrackingUrl($trackingUrl)
    {
        return $this->setData(ShipmentTrackInterface::TRACKING_URL, $trackingUrl);
    }

    /**
     * @inheritDoc
     */
    public function getTrackingUrl()
    {

        return $this->getData(ShipmentTrackInterface::TRACKING_URL);
    }

    protected function _construct()
    {
        $this->setIdFieldName(ShipmentTrackInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }
}
