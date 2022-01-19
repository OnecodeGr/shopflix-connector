<?php
/**
 * View.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Onecode\ShopFlixConnector\Model\Order\ShipmentLoader;

class View extends Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::shipment';

    /**
     * @var ShipmentLoader
     */
    private $shipmentLoader;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;


    /**
     * @param Action\Context $context
     * @param ShipmentLoader $shipmentLoader
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        ShipmentLoader $shipmentLoader,
        PageFactory    $resultPageFactory
    )
    {
        $this->shipmentLoader = $shipmentLoader;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
        $shipment = $this->shipmentLoader->load();
        if ($shipment) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->getBlock('shopflix_shipment_view');
            $resultPage->setActiveMenu('Onecode_ShopFlixConnector::shipment');
            $resultPage->getConfig()->getTitle()->prepend(__('Shipments'));
            $resultPage->getConfig()->getTitle()->prepend("#" . $shipment->getIncrementId());
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('shopflix/shipment/index');
            return $resultRedirect;
        }
    }
}
