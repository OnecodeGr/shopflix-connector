<?php
/**
 * UpdateOrder.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use GuzzleHttp\Exception\GuzzleException;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order;
use Psr\Log\LoggerInterface;

class UpdateOrder
{

    private $_helper;
    private $_logger;
    private $_orderManagement;


    public function __construct(
        Data                $data,
        LoggerInterface     $logger,
        ManagementInterface $orderManagement
    )
    {
        $this->_helper = $data;

        $this->_logger = $logger;
        $this->_orderManagement = $orderManagement;
    }


    /**
     * @param Order $order
     * @return void
     * @throws GuzzleException
     */
    public function update(Order $order)
    {

        if (!$this->_helper->isEnabled()) {
            return;
        }
        $connector = new Connector(
            $this->_helper->getUsername(),
            $this->_helper->getApikey(),
            $this->_helper->getApiUrl(),
            $this->_helper->getTimeModifier()
        );

        $shopFlixData = $connector->getOrderDetail($order->getIncrementId());

        $status = $shopFlixData['order']['status'];
        $state = $shopFlixData['order']['state'];

        if ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_READY_TO_BE_SHIPPED
            && $order->canReadyToBeShipped()) {
            $this->_orderManagement->readyToBeShipped($order->getId(), true);
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_ON_THE_WAY && $order->canOnTheWay()) {
            $this->_orderManagement->onTheWay($order->getId());
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_PARTIAL_SHIPPED && $order->canPartialShipped()) {
            $this->_orderManagement->partialShipped($order->getId());
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_SHIPPED && $order->canShipped()) {
            $this->_orderManagement->shipped($order->getId());
        } elseif ($state == OrderInterface::STATE_COMPLETED &&
            $status == StatusInterface::STATUS_COMPLETED && $order->canCompleted()) {
            $this->_orderManagement->completed($order->getId());
        } elseif ($state == OrderInterface::STATE_CANCELED &&
            $status == StatusInterface::STATUS_CANCELED && $order->canCancel()) {
            $this->_orderManagement->cancel($order->getId());
        } elseif ($state == OrderInterface::STATE_ACCEPTED &&
            ($status == StatusInterface::STATUS_ACCEPTED || $status == StatusInterface::STATUS_PICKING)
            && $order->canAccept()) {
            $this->_orderManagement->accept($order->getId(), true);
        } elseif ($state == OrderInterface::STATE_REJECTED &&
            $status == StatusInterface::STATUS_REJECTED
            && $order->canReject()) {
            $this->_orderManagement->reject($order->getId(), '', true);
        }

    }


}
