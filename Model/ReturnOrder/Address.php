<?php
/**
 * Address.php
 *
 * @copyright Copyright © 2022 Onecode P.C.  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\ReturnOrder;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\AddressInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderAddressInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Address as ResourceModel;
use Onecode\ShopFlixConnector\Model\ReturnOrder;
use Onecode\ShopFlixConnector\Model\ReturnOrderFactory;

class Address extends AbstractModel implements ReturnOrderAddressInterface
{
    /**
     * Possible customer address types
     */
    const TYPE_BILLING = 'billing';

    const TYPE_SHIPPING = 'shipping';
    /**
     * @var ReturnOrder
     */
    protected $order;

    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order_address_model';

    /**
     * @var string
     */
    protected $_eventObject = 'address';

    /**
     * @var ReturnOrderFactory
     */
    protected $orderFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;
    /**
     * @var ReturnOrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        Context                        $context,
        Registry                       $registry,
        RegionFactory                  $regionFactory,
        ReturnOrderRepositoryInterface $orderRepository,
        AbstractResource               $resource = null,
        AbstractDb                     $resourceCollection = null,
        array                          $data = []
    )
    {
        $data = $this->implodeStreetField($data);
        $this->regionFactory = $regionFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Implode value of the street field, if it is present among other fields
     *
     * @param array $data
     * @return array
     */
    protected function implodeStreetField(array $data)
    {
        if (array_key_exists(AddressInterface::STREET, $data)) {
            $data[AddressInterface::STREET] = $this->implodeStreetValue($data[AddressInterface::STREET]);
        }
        return $data;
    }

    protected function implodeStreetValue($value)
    {
        if (is_array($value)) {
            $value = trim(implode(PHP_EOL, $value));
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getAddressType(): string
    {
        return $this->_getData(ReturnOrderAddressInterface::ADDRESS_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->_getData(ReturnOrderAddressInterface::CITY);
    }

    /**
     * @inheritDoc
     */
    public function getCompany()
    {
        return $this->_getData(ReturnOrderAddressInterface::COMPANY);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->_getData(ReturnOrderAddressInterface::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return $this->_getData(ReturnOrderAddressInterface::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(ReturnOrderAddressInterface::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getFax()
    {
        return $this->_getData(ReturnOrderAddressInterface::FAX);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname()
    {
        return $this->_getData(ReturnOrderAddressInterface::FIRSTNAME);
    }

    /**
     * @inheritDoc
     */
    public function getLastname()
    {
        return $this->_getData(ReturnOrderAddressInterface::LASTNAME);
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return $this->_getData(ReturnOrderAddressInterface::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->_getData(ReturnOrderAddressInterface::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function getRegionCode()
    {
        $regionId = (!$this->getRegionId() && is_numeric($this->getRegion())) ?
            $this->getRegion() :
            $this->getRegionId();
        $model = $this->regionFactory->create()->load($regionId);
        if ($model->getCountryId() == $this->getCountryId()) {
            return $model->getCode();
        } elseif (is_string($this->getRegion())) {
            return $this->getRegion();
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRegionId()
    {
        return $this->_getData(ReturnOrderAddressInterface::REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->_getData(ReturnOrderAddressInterface::REGION);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->_getData(ReturnOrderAddressInterface::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->_getData(ReturnOrderAddressInterface::STREET);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->_getData(ReturnOrderAddressInterface::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($id)
    {
        return $this->setData(ReturnOrderAddressInterface::REGION_ID, $id);

    }

    /**
     * @inheritDoc
     */
    public function setFax($fax)
    {
        return $this->setData(ReturnOrderAddressInterface::FAX, $fax);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(ReturnOrderAddressInterface::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(ReturnOrderAddressInterface::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname)
    {
        return $this->setData(ReturnOrderAddressInterface::LASTNAME, $lastname);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($street)
    {
        return $this->setData(ReturnOrderAddressInterface::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(ReturnOrderAddressInterface::CITY, $city);

    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(ReturnOrderAddressInterface::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(ReturnOrderAddressInterface::TELEPHONE, $telephone);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($id)
    {
        return $this->setData(ReturnOrderAddressInterface::COUNTRY_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setFirstname($firstname)
    {
        return $this->setData(ReturnOrderAddressInterface::FIRSTNAME, $firstname);
    }

    /**
     * @inheritDoc
     */
    public function setAddressType($addressType)
    {
        return $this->setData(ReturnOrderAddressInterface::ADDRESS_TYPE, $addressType);
    }

    /**
     * @inheritDoc
     */
    public function setCompany($company)
    {
        return $this->setData(ReturnOrderAddressInterface::COMPANY, $company);
    }

    /**
     * @inheritDoc
     */
    public function setRegionCode($regionCode)
    {
        return $this->setData(ReturnOrderAddressInterface::KEY_REGION_CODE, $regionCode);
    }

    public function setOrder(ReturnOrderInterface $order)
    {
        $this->order = $order;
        $this->setParentId($order->getId());
        return $this;
    }
    /**
     * @return ReturnOrder
     * @throws NoSuchEntityException
     */
    public function getOrder(): ReturnOrder
    {
        if ($this->order === null && ($orderId = $this->getParentId())) {
            $order = $this->orderRepository->getById($orderId);
            $this->setOrder($order);
        }

        return $this->order;
    }
    /**
     * @inheritDoc
     */
    public function setParentId($id)
    {
        return $this->setData(ReturnOrderAddressInterface::PARENT_ID, $id);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setIdFieldName(ReturnOrderAddressInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }
}
