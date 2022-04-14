<?php
/**
 * NotSyncedDataProviderInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Provider;

/**
 * Interface provides entities id list that should be updated in grid
 */
interface NotSyncedDataProviderInterface
{
    /**
     * Returns id list of entities for adding or updating in grid.
     *
     * @param string $mainTableName source table name
     * @param string $gridTableName grid table name
     * @return array
     */
    public function getIds($mainTableName, $gridTableName);
}
