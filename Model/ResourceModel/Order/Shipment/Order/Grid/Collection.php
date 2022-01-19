<?php
/**
 * Collection.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment\Order\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Shipment;
use Psr\Log\LoggerInterface as Logger;

class Collection extends SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger        $logger,
        FetchStrategy $fetchStrategy,
        EventManager  $eventManager,
        $mainTable = 'onecode_shopflix_shipment_grid',
        $resourceModel = Shipment::class
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }
}
