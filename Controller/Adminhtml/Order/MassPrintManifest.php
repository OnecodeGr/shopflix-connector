<?php
/**
 * MassPrintManifest.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

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
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\CollectionFactory;

class MassPrintManifest extends AbstractMassAction implements HttpPostActionInterface
{

    private $downloader;
    private $helper;


    public function __construct(Context            $context,
                                Filter             $filter,
                                CollectionFactory  $collectionFactory,
                                FileFactory        $fileFactory,
                                Data               $data)
    {
        $this->downloader = $fileFactory;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $data;
        parent::__construct($context, $filter);

    }

    protected function massAction(AbstractCollection $collection)
    {
        foreach ($collection->getItems() as $item) {
            $order = $item->getOrder();
            if ($order) {
                break;
            }
        }

        try {

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
                return $this->downloader->create("manifest_order_{$order->getIncrementId()}_$date.pdf",
                    $fileContent, DirectoryList::VAR_DIR, 'application/pdf');
            } else {
                throw new LocalizedException(__("We can not print manifest for empty shipments"));
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('shopflix/order/view', ['order_id' => $order->getId()]);
        }
    }
}
