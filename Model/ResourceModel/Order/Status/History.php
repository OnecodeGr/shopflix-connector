<?php
/**
 * History.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Onecode\ShopFlixConnector\Model\Order\Status\History\Validator;
use Onecode\ShopFlixConnector\Model\Spi\StatusHistoryResourceInterface;

class History extends AbstractDb implements StatusHistoryResourceInterface
{
    protected $_eventPrefix = 'onecode_shopflix_order_status_history_resource';


    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(Context           $context,
                                Snapshot          $entitySnapshot,
                                RelationComposite $entityRelationComposite,
                                Validator         $validator,
                                                  $connectionName = null)
    {
        $this->validator = $validator;
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('onecode_shopflix_order_status_history', 'entity_id');
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);
        $warnings = $this->validator->validate($object);
        if (!empty($warnings)) {
            throw new LocalizedException(
                __("Cannot save comment:\n%1", implode("\n", $warnings))
            );
        }
        return $this;
    }
}
