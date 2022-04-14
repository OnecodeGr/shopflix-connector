<?php
/**
 * UpdateReturnOrder.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;


use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderManagementInterface;
use Onecode\ShopFlixConnector\Library\Connector;
use Psr\Log\LoggerInterface;

class UpdateReturnOrder
{
    private $_helper;
    private $_logger;
    private $_orderManagement;


    public function __construct(
        Data                           $data,
        LoggerInterface                $logger,
        ReturnOrderManagementInterface $orderManagement
    )
    {
        $this->_helper = $data;
        $this->_logger = $logger;
        $this->_orderManagement = $orderManagement;
    }

    /**
     * @param ReturnOrderInterface $order
     * @return void
     */
    public function update(ReturnOrderInterface $order)
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

        $shopFlixData = $connector->getReturnOrderDetail($order->getIncrementId(), []);


        $status = $shopFlixData['return_order']['status'];
        $state = $shopFlixData['return_order']['state'];

       if ($state == ReturnOrderInterface::STATE_APPROVED &&
            $status == ReturnOrderStatusInterface::STATUS_RETURN_APPROVED && $order->canApprove()) {
            $this->_orderManagement->approved($order->getId(), false);
        } elseif ($state == ReturnOrderInterface::STATE_DECLINED &&
            $status == ReturnOrderStatusInterface::STATUS_RETURN_DECLINED && $order->canDecline()) {
            $this->_orderManagement->declined($order->getEntityId(), '', false);
        } elseif ($state == ReturnOrderInterface::STATE_DELIVERED_TO_THE_STORE &&
            $status == ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE && $order->canDelivered()) {
            $this->_orderManagement->delivered($order->getId());
        } elseif ($state == ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX &&
            ($status == ReturnOrderStatusInterface::STATUS_ON_THE_WAY_TO_THE_STORE)
            && $order->canOnTheWay()) {
            $this->_orderManagement->onTheWay($order->getId());
        }

    }
}
