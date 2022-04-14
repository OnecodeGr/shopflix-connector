<?php
/**
 * Address.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\AddressInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Model\Order;
use Onecode\ShopFlixConnector\Model\OrderFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Address as ResourceModel;

class Address extends AbstractModel implements AddressInterface
{

    /**
     * Possible customer address types
     */
    const TYPE_BILLING = 'billing';

    const TYPE_SHIPPING = 'shipping';
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var string
     */
    protected $_eventPrefix = 'shopflix_order_address';

    /**
     * @var string
     */
    protected $_eventObject = 'address';

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param OrderFactory $orderFactory
     * @param RegionFactory $regionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                    $context,
        Registry                   $registry,
        OrderRepositoryInterface   $orderRepository,
        RegionFactory              $regionFactory,
        AbstractResource           $resource = null,
        AbstractDb                 $resourceCollection = null,
        array                      $data = []
    )
    {
        $data = $this->implodeStreetField($data);
        $this->regionFactory = $regionFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection);
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

    /**
     * Combine values of street lines into a single string
     *
     * @param string[]|string $value
     * @return string
     */
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
    public function getAddressType()
    {
        return $this->_getData(AddressInterface::ADDRESS_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->_getData(AddressInterface::CITY);
    }

    /**
     * @inheritDoc
     */
    public function getCompany()
    {
        return $this->_getData(AddressInterface::COMPANY);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->_getData(AddressInterface::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return $this->_getData(AddressInterface::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(AddressInterface::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getFax()
    {
        return $this->_getData(AddressInterface::FAX);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname()
    {
        return $this->_getData(AddressInterface::FIRSTNAME);
    }

    /**
     * @inheritDoc
     */
    public function getLastname()
    {
        return $this->_getData(AddressInterface::LASTNAME);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->_getData(AddressInterface::POSTCODE);
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
        return $this->_getData(AddressInterface::REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->_getData(AddressInterface::REGION);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->_getData(AddressInterface::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->_getData(AddressInterface::STREET);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->_getData(AddressInterface::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($id)
    {
        return $this->setData(AddressInterface::REGION_ID, $id);

    }

    /**
     * @inheritDoc
     */
    public function setFax($fax)
    {
        return $this->setData(AddressInterface::FAX, $fax);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(AddressInterface::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(AddressInterface::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname)
    {
        return $this->setData(AddressInterface::LASTNAME, $lastname);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($street)
    {
        return $this->setData(AddressInterface::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(AddressInterface::CITY, $city);

    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(AddressInterface::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(AddressInterface::TELEPHONE, $telephone);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($id)
    {
        return $this->setData(AddressInterface::COUNTRY_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setFirstname($firstname)
    {
        return $this->setData(AddressInterface::FIRSTNAME, $firstname);
    }

    /**
     * @inheritDoc
     */
    public function setAddressType($addressType)
    {
        return $this->setData(AddressInterface::ADDRESS_TYPE, $addressType);
    }

    /**
     * @inheritDoc
     */
    public function setCompany($company)
    {
        return $this->setData(AddressInterface::COMPANY, $company);
    }

    /**
     * @inheritDoc
     */
    public function setRegionCode($regionCode)
    {
        return $this->setData(AddressInterface::KEY_REGION_CODE, $regionCode);
    }

    /**
     * @return Order
     * @throws NoSuchEntityException
     */
    public function getOrder(): Order
    {
        if ($this->order === null && ($orderId = $this->getParentId())) {
            $order = $this->orderRepository->getById($orderId);
            $this->setOrder($order);
        }

        return $this->order;
    }

    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
        $this->setParentId($order->getId());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return $this->_getData(AddressInterface::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setParentId($id)
    {
        return $this->setData(AddressInterface::PARENT_ID, $id);
    }

    public function _construct()
    {
        $this->setIdFieldName(AddressInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }


}
