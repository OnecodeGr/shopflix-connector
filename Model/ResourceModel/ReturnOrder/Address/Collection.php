<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Address;

use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Address as ResourceModel;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Collection\AbstractCollection;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Address as Model;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_address_collection';


    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }



    protected function _afterLoad(): Collection
    {
        parent::_afterLoad();

        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', [$this->_eventObject => $this]);

        return $this;
    }

}
