<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\ReturnOrder;

class Index extends ReturnOrder implements HttpGetActionInterface
{
    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('SHOPFLIX Return Orders'));
        return $resultPage;
    }
}
