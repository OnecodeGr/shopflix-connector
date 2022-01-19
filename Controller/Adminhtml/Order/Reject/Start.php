<?php
/**
 * Start.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order\Reject;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Reject\AbstractReject\View;

class Start extends View implements HttpGetActionInterface
{
    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('shopflix/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
        return $resultRedirect;
    }
}
