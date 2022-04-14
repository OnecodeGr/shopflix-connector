<?php
/**
 * AbstractGrid.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

abstract class AbstractGrid extends AbstractDb implements GridInterface
{
    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $gridTableName;

    /**
     * @var string
     */
    protected $orderTableName = 'onecode_shopflix_order';

    /**
     * @var string
     */
    protected $addressTableName = 'onecode_shopflix_address';

    /**
     * Returns grid table name
     *
     * @return string
     */
    public function getGridTable()
    {
        return $this->getTable($this->gridTableName);
    }

    /**
     * Purge grid row
     *
     * @param int|string $value
     * @param null|string $field
     * @return int
     */
    public function purge($value, $field = null)
    {
        return $this->getConnection()->delete(
            $this->getTable($this->gridTableName),
            [($field ?: 'entity_id') . ' = ?' => $value]
        );
    }

    /**
     * Returns connection
     *
     * @return AdapterInterface
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resources->getConnection('sales');
        }
        return $this->connection;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        //
    }

    /**
     * Returns update time of the last row in the grid.
     *
     * If there are no rows in the grid, default value will be returned.
     *
     * @param string $default
     * @return string
     * @deprecated 101.0.0 this method is not used in abstract model but only in single child so
     * this deprecation is a part of cleaning abstract classes.
     * @see \Onecode\ShopFlixConnector\Model\ResourceModel\Provider\UpdatedIdListProvider
     */
    protected function getLastUpdatedAtValue($default = '0000-00-00 00:00:00')
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable($this->gridTableName), ['updated_at'])
            ->order('updated_at DESC')
            ->limit(1);

        $row = $this->getConnection()->fetchRow($select);

        return $row['updated_at'] ?? $default;
    }
}
