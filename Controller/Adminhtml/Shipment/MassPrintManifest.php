<?php
/**
 * MassPrintManifest.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Onecode\ShopFlixConnector\Api\Data\ShipmentInterface;
use Onecode\ShopFlixConnector\Api\Data\ShipmentTrackInterface;
use Onecode\ShopFlixConnector\Helper\Data;
use Onecode\ShopFlixConnector\Library\Connector;
use Onecode\ShopFlixConnector\Model\Order\Shipment\TrackFactory;
use Onecode\ShopFlixConnector\Model\Order\Shipment\TrackRepository;
use Onecode\ShopFlixConnector\Model\Order\ShipmentRepository;
use Onecode\ShopFlixConnector\Model\OrderRepository;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\CollectionFactory;

class MassPrintManifest extends AbstractMassAction implements HttpPostActionInterface
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
    /**
     * @var Data
     */
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

            $shipments = [];
            /** @var ShipmentInterface $shipment */
            foreach ($collection->getItems() as $shipment) {
                /** @var ShipmentTrackInterface $track */
                if ($shipment->getTracks()) {
                    foreach ($shipment->getTracks() as $track) {
                        if ($track->getTrackNumber()) {
                            $shipments[] = $shipment->getIncrementId();
                        }
                    }
                }
            }
            $shipments = array_unique($shipments);
            if ($shipments) {
                $manifest = $connector->printManifest($shipments);
                if($manifest['status']=="error"){
                    throw new LocalizedException(__("SHOPFLIX: %1",[$manifest['manifest']]));
                }
                $fileContent = base64_decode($manifest['manifest']);
                $content = [
                    "type" => "string",
                    "value" => $fileContent,
                    "rm" => true
                ];
                return $this->downloader->create("manifest-" . $date . ".pdf",
                    $content, DirectoryList::VAR_DIR);
            } else {
                throw new LocalizedException(__("We can not print manifest for empty shipments"));
            }

        } catch (Exception $e) {

            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('shopflix/shipment/index');
        }
    }
}
