<?php
/**
 * PrintAction.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment\AbstractShipment;

use Exception;
use GuzzleHttp\Exception\ServerException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Helper\Data;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order\Shipment;
use Onecode\ShopFlixConnector\Model\Order\ShipmentRepository;
use Onecode\ShopFlixConnector\Model\OrderRepository;

abstract class PrintAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::shipment';

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Shipment\TrackRepository
     */
    private $trackRepository;
    /**
     * @var Shipment\TrackFactory
     */
    private $trackFactory;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    private $helper;

    public function __construct(
        Context                  $context,
        Data                     $data,
        FileFactory              $fileFactory,
        OrderRepository          $orderRepository,
        ForwardFactory           $resultForwardFactory,
        Shipment\TrackFactory    $trackFactory,
        Shipment\TrackRepository $trackRepository,
        ShipmentRepository       $shipmentRepository
    )
    {
        $this->_fileFactory = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->trackRepository = $trackRepository;
        $this->trackFactory = $trackFactory;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->helper = $data;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Forward
     * @throws Exception
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        try {
            if ($shipmentId) {
                $connector = new Connector($this->helper->getUsername(), $this->helper->getApikey(), $this->helper->getApiUrl());
                /** @var Shipment $shipment */
                $shipment = $this->_objectManager->create(Shipment::class)->load($shipmentId);
                if ($shipment) {

                    $date = $this->_objectManager->get(DateTime::class)->date('Y-m-d_H-i-s');

                    $track = $shipment->getAllTracks();
                    if (!count($track)) {
                        $track = $this->createVoucher($connector, $shipment);
                    } else {
                        $track = $track[0];
                    }

                    if ($track) {
                        $voucherPdf = $connector->printVoucher($track->getTrackNumber());
                        $fileContent = base64_decode($voucherPdf['Voucher']);

                        if ($shipment->getOrder()->getState() === OrderInterface::STATE_ACCEPTED) {
                            $this->orderRepository->save($shipment->getOrder()
                                ->setStatus(StatusInterface::STATUS_READY_TO_BE_SHIPPED)
                                ->setState(OrderInterface::STATE_COMPLETED));
                        }

                        if ($shipment->getShipmentStatus() == 1) {
                            $connector->forShipment($shipment->getIncrementId());
                        }
                        $shipment->setShipmentStatus(2);
                        $this->shipmentRepository->save($shipment);
                        return $this->_fileFactory->create(
                            $track->getTrackNumber() . $date . '.pdf',
                            $fileContent,
                            DirectoryList::VAR_DIR,
                            'application/pdf');
                    } else {
                        throw new LocalizedException(__("We can not print the voucher try again later"));
                    }
                }
            } else {
                /** @var Forward $resultForward */
                $resultForward = $this->resultForwardFactory->create();
                return $resultForward->forward('noroute');
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('shopflix/shipment/view', ['shipment_id' => $shipmentId]);
        }

    }

    private function createVoucher(Connector $connector, ShipmentInterface $shipment)
    {

        try {
            $voucher = $connector->createVoucher($shipment->getIncrementId());
            $voucher = $voucher['voucher']['ShipmentNumber'];
        } catch (ServerException $e) {
            $voucher = $connector->getVoucher($shipment->getIncrementId());
        }

        dd($shipment->getTracks());

        if ($voucher) {
            $trackingUrl = $connector->getShipmentUrl($shipment->getIncrementId());
            $track = $this->trackFactory->create();
            $track->setOrderId($shipment->getOrderId())
                ->setParentId($shipment->getId())
                ->setTrackNumber($voucher)
                ->setTrackingUrl($trackingUrl);
            return $this->trackRepository->save($track);

        }
        return null;

    }
}
