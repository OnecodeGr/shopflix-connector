<?php
/**
 * MassPrintVoucher.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment;

use Exception;
use GuzzleHttp\Exception\ServerException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Helper\Data;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order\Shipment\TrackFactory;
use Onecode\ShopFlixConnector\Model\Order\Shipment\TrackRepository;
use Onecode\ShopFlixConnector\Model\Order\ShipmentRepository;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\CollectionFactory;


class MassPrintVoucher extends AbstractMassAction implements HttpPostActionInterface
{

    /**
     * @var FileFactory
     */
    private $downloader;
    /**
     * @var TrackFactory
     */
    private $trackFactory;
    /**
     * @var TrackRepository
     */
    private $trackRepository;
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    private $helper;

    public function __construct(Context            $context,
                                Filter             $filter,
                                CollectionFactory  $collectionFactory,
                                TrackFactory       $trackFactory,
                                TrackRepository    $trackRepository,
                                ShipmentRepository $shipmentRepository,
                                OrderRepository    $orderRepository,
                                FileFactory        $fileFactory,
                                Data               $data)
    {
        $this->trackFactory = $trackFactory;
        $this->trackRepository = $trackRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        $this->downloader = $fileFactory;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $data;
        parent::__construct($context, $filter);

    }

    protected function massAction(AbstractCollection $collection)
    {
        try {

            /** @var Connector $connector */
            $connector = new Connector($this->helper->getUsername(), $this->helper->getApikey(), $this->helper->getApiUrl());
            $date = $this->_objectManager->get(
                DateTime::class
            )->date('Y-m-d_H-i-s');

            $tracks = [];

            /** @var ShipmentInterface $shipment */
            foreach ($collection->getItems() as $shipment) {
                /** @var ShipmentTrackInterface $track */
                $track = $shipment->getAllTracks();
                if (!count($track)) {
                    $tracks[] = $this->createVoucher($connector, $shipment);
                } else {
                    $tracks[] = $track[0]->getTrackNumber();
                }
            }


            if (count($tracks) > 20) {
                throw new LocalizedException(__("We can print 20 vouchers at once you have selected %1", count($tracks)));
            }
            $voucherPdf = $connector->printVouchers($tracks);

            $fileContent = base64_decode($voucherPdf[0]['Voucher']);

            foreach ($collection->getItems() as $shipment) {
                $trackExist = false;
                foreach ($shipment->getTracks() as $track) {
                    if (in_array($track->getTrackNumber(), $tracks)) {
                        $trackExist = true;
                    }
                }

                if ($trackExist) {
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
                }
            }


            return $this->downloader->create("shopflix_vouchers-" . $date . ".pdf",
                $fileContent, DirectoryList::VAR_DIR, 'application/pdf');
        } catch (Exception $e) {
            #$this->logger->info($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('shopflix/shipment/index');
        }
    }

    /**
     * @param Connector $connector
     * @param ShipmentInterface $shipment
     * @return string|null
     * @throws CouldNotSaveException
     */
    private function createVoucher(Connector $connector, ShipmentInterface $shipment): ?string
    {

        try {
            $voucher = $connector->createVoucher($shipment->getIncrementId());
            $voucher = $voucher['voucher']['ShipmentNumber'];
        } catch (ServerException $e) {
            $voucher = $connector->getVoucher($shipment->getIncrementId());
        }

        if ($voucher) {
            $trackingUrl = $connector->getShipmentUrl($shipment->getIncrementId());
            $track = $this->trackFactory->create();
            $track->setOrderId($shipment->getOrderId())
                ->setParentId($shipment->getId())
                ->setTrackNumber($voucher)
                ->setTrackingUrl($trackingUrl);
            $this->trackRepository->save($track);
            return $track->getTrackNumber();
        }
        return null;
    }
}
