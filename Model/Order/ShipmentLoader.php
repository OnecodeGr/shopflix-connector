<?php
/**
 * ShipmentLoader.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Api\ShipmentRepositoryInterface;
#use Onecode\ShopFlixConnector\Model\Order\ShipmentDocumentFactory;
/**
 * Loader for shipment
 *
 * @method ShipmentLoader setOrderId($id)
 * @method ShipmentLoader setShipmentId($id)
 * @method ShipmentLoader setShipment($shipment)
 * @method ShipmentLoader setTracking($tracking)
 * @method int getOrderId()
 * @method int getShipmentId()
 * @method array getTracking()
 */
class ShipmentLoader extends DataObject
{

    const SHIPMENT = 'shipment';
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShipmentTrackCreationInterfaceFactory
     */
    private $trackFactory;

    /**
     * @var ShipmentItemCreationInterfaceFactory
     */
    private $itemFactory;

    /**
     * @param ManagerInterface $messageManager
     * @param Registry $registry
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentDocumentFactory $documentFactory
     * @param array $data
     */
    public function __construct(
        ManagerInterface $messageManager,
        Registry $registry,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        $this->registry = $registry;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct($data);
    }

    /**
     * Initialize shipment model instance
     *
     * @return bool|\Magento\Sales\Model\Order\Shipment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load()
    {
        $shipment = false;
        $orderId = $this->getOrderId();
        $shipmentId = $this->getShipmentId();
        if ($shipmentId) {
            try {
                $shipment = $this->shipmentRepository->get($shipmentId);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This shipment no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = $this->orderRepository->get($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->messageManager->addError(__('The order no longer exists.'));
                return false;
            }

        }

        $this->registry->register('current_shopflix_shipment', $shipment);
        return $shipment;
    }





    /**
     * Retrieve shipment
     *
     * @return array
     */
    public function getShipment()
    {
        return $this->getData(self::SHIPMENT) ?: [];
    }
}
