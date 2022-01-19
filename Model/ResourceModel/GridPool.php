<?php
/**
 * GridPool.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel;

class GridPool
{
    /**
     * @var Grid[]
     */
    protected $grids;

    /**
     * @param array $grids
     */
    public function __construct(array $grids)
    {
        $this->grids = $grids;
    }

    /**
     * Refresh grids list
     *
     * @param int $orderId
     * @return GridPool
     */
    public function refreshByOrderId($orderId)
    {
        foreach ($this->grids as $grid) {
            $grid->refresh($orderId, $grid->getOrderIdField());
        }

        return $this;
    }
}
