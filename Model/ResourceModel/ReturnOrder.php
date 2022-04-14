<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Onecode\ShopFlixConnector\Model\Spi\ReturnOrderResourceInterface;

class ReturnOrder extends AbstractDb implements ReturnOrderResourceInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('onecode_shopflix_return_order', 'entity_id');
        $this->_useIsObjectNew = true;
    }

    /**
     * Count existent products of order items by specified product types
     *
     * @param int $orderId
     * @param array $productTypeIds
     * @param bool $isProductTypeIn
     * @return array
     */
    public function aggregateProductsByTypes($orderId, $productTypeIds = [],
                                             $isProductTypeIn = false)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['o' => $this->getTable('onecode_shopflix_return_order_item')],
                ['o.product_type', new \Zend_Db_Expr('COUNT(*)')]
            )
            ->where('o.order_id=?', $orderId)
            ->where('o.product_id IS NOT NULL')
            ->group('o.product_type');
        if ($productTypeIds) {
            $select->where(
                sprintf(
                    '(o.product_type %s (?))',
                    $isProductTypeIn ? 'IN' : 'NOT IN'
                ),
                $productTypeIds
            );
        }
        return $connection->fetchPairs($select);
    }

}
