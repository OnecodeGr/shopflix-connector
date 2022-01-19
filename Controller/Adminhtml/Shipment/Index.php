<?php
/**
 * Index.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Shipment\AbstractShipment\Index as AbstractIndex;

class Index extends AbstractIndex implements HttpGetActionInterface
{

}
