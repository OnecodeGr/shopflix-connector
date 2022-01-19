<?php
/**
 * DebugHandler.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class DebugHandler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;


    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/onecode_shopflix/debug.log';
}
