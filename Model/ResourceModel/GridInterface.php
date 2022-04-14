<?php
/**
 * GridInterface.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel;

use Zend_Db_Statement_Interface;

interface GridInterface
{
    /**
     * Adds new rows to the grid.
     *
     * Only rows that correspond to $value and $field parameters should be added.
     *
     * @param int|string $value
     * @param null|string $field
     * @return Zend_Db_Statement_Interface
     */
    public function refresh($value, $field = null);

    /**
     * Adds new rows to the grid.
     *
     * Only rows created/updated since the last method call should be added.
     *
     * @return void
     */
    public function refreshBySchedule();


    /**
     * @param int|string $value
     * @param null|string $field
     * @return int
     */
    public function purge($value, $field = null);
}
