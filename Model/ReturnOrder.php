<?php
/**
 * ReturnOrder.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region as RegionResource;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderAddressInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderItemInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\ReturnOrderStatusInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Api\ReturnOrderItemRepositoryInterface;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder as ResourceModel;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Address\Collection;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Address\CollectionFactory as OrderAddressCollectionFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Item\Collection as ItemCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Item\CollectionFactory as OrderItemCollectionFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Status\History\Collection as HistoryCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\ReturnOrder\Status\History\CollectionFactory as OrderHistoryCollectionFactory;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Config;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Status\History;
use Onecode\ShopFlixConnector\Model\ReturnOrder\Status\HistoryFactory;

/**
 * @method setAddresses(array $array_merge)
 */
class ReturnOrder extends AbstractModel implements ReturnOrderInterface
{

    /**
     * @var string[]
     */
    protected $interfaceAttributes = self::ATTRIBUTES;
    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_return_order';
    protected $_baseCurrency = null;
    /**
     * @var SearchCriteriaBuilder|mixed
     */
    private $searchCriteriaBuilder;
    private $itemRepository;
    /**
     * @var OrderItemCollectionFactory
     */
    private $_orderItemCollectionFactory;
    /**
     * @var OrderHistoryCollectionFactory
     */
    private $_historyCollectionFactory;
    /**
     * @var CurrencyFactory
     */
    private $_currencyFactory;
    /**
     * @var OrderAddressCollectionFactory
     */
    private $_addressCollectionFactory;
    /**
     * @var array
     */
    private $regionItems;
    /**
     * @var RegionFactory|mixed
     */
    private $regionFactory;
    /**
     * @var RegionResource|mixed
     */
    private $regionResource;
    /**
     * @var Config
     */
    private $_orderConfig;
    /**
     * @var HistoryFactory
     */
    private $_orderHistoryFactory;
    /**
     * @var OrderRepositoryInterface
     */
    private $_orderRepository;

    public function __construct(
        Context                            $context,
        Registry                           $registry,
        OrderAddressCollectionFactory      $addressCollectionFactory,
        OrderItemCollectionFactory         $orderItemCollectionFactory,
        OrderHistoryCollectionFactory      $historyCollectionFactory,
        ReturnOrderItemRepositoryInterface $itemRepository,
        CurrencyFactory                    $currencyFactory,
        Config                             $config,
        HistoryFactory                     $orderHistoryFactory,
        OrderRepositoryInterface           $orderRepository,
        AbstractResource                   $resource = null,
        AbstractDb                         $resourceCollection = null,
        array                              $data = [],
        RegionFactory                      $regionFactory = null,
        RegionResource                     $regionResource = null,
        SearchCriteriaBuilder              $searchCriteriaBuilder = null

    )
    {
        $this->_addressCollectionFactory = $addressCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder ?: ObjectManager::getInstance()
            ->get(SearchCriteriaBuilder::class);
        $this->regionFactory = $regionFactory ?: ObjectManager::getInstance()->get(RegionFactory::class);
        $this->regionResource = $regionResource ?: ObjectManager::getInstance()->get(RegionResource::class);
        $this->regionItems = [];
        $this->itemRepository = $itemRepository;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_historyCollectionFactory = $historyCollectionFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_orderConfig = $config;
        $this->_orderHistoryFactory = $orderHistoryFactory;
        $this->_orderRepository = $orderRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function setParentId(int $parentId): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::PARENT_ORDER_ID, $parentId);
    }

    public function setShopFlixParentOrderId(int $shopflixParentOrderId): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::SHOPFLIX_PARENT_ORDER_ID, $shopflixParentOrderId);
    }

    public function getSubtotal(): float
    {
        return $this->getData(ReturnOrderInterface::SUBTOTAL);
    }

    public function setTotalPaid(float $totalPaid): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::TOTAL_PAID, $totalPaid);
    }

    public function getCustomerNote(): string
    {
        return $this->getData(ReturnORderInterface::CUSTOMER_NOTE);
    }

    public function getShippingAddressId(): ?int
    {
        return $this->getData(ReturnOrderInterface::SHIPPING_ADDRESS_ID);
    }

    public function setShippingAddressId(int $shippingAddressId): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::SHIPPING_ADDRESS_ID, $shippingAddressId);
    }

    public function getBillingAddressId(): ?int
    {
        return $this->getData(ReturnOrderInterface::BILLING_ADDRESS_ID);
    }

    public function setBillingAddressId(int $billingAddressId): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::BILLING_ADDRESS_ID, $billingAddressId);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(ReturnOrderInterface::UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::UPDATED_AT, $updatedAt);
    }

    public function setShopFlixOrderId(int $shopflixOrderId): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::SHOPFLIX_ORDER_ID, $shopflixOrderId);
    }

    public function getParentId(): int
    {
        return $this->getData(ReturnOrderInterface::PARENT_ORDER_ID);
    }

    public function getShopFlixParentOrderId(): int
    {
        return $this->getData(ReturnOrderInterface::SHOPFLIX_PARENT_ORDER_ID);
    }

    /**
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getParentOrder(): OrderInterface
    {
        return $this->_orderRepository->getById($this->getParentId());
    }

    public function getShopFlixOrderId(): int
    {
        return $this->getData(ReturnOrderInterface::SHOPFLIX_ORDER_ID);
    }

    public function setSubtotal(float $subtotal): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::SUBTOTAL, $subtotal);
    }

    public function getTotalPaid(): float
    {
        return $this->getData(ReturnOrderInterface::TOTAL_PAID);
    }

    public function getIncrementId(): string
    {
        return $this->getData(ReturnOrderInterface::INCREMENT_ID);
    }

    public function setIncrementId(string $incrementId): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::INCREMENT_ID, $incrementId);
    }

    public function setCustomerEmail(string $customerEmail): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::CUSTOMER_EMAIL, $customerEmail);
    }

    public function setCustomerFirstname(string $customerFirstname): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::CUSTOMER_FIRSTNAME, $customerFirstname);
    }

    public function setCustomerLastname(string $customerLastname): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::CUSTOMER_LASTNAME, $customerLastname);
    }

    public function getRemoteIp(): ?string
    {
        return $this->getData(ReturnOrderInterface::CUSTOMER_REMOTE_IP);
    }

    public function setRemoteIP(string $remoteIp): ReturnOrderInterface
    {
        return $this->getData(ReturnOrderInterface::CUSTOMER_REMOTE_IP, $remoteIp);
    }

    public function setCustomerNote(): ReturnOrderInterface
    {
        return $this->getData(ReturnOrderInterface::CUSTOMER_NOTE);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(ReturnorderInterface::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::CREATED_AT, $createdAt);
    }

    public function getStatusLabel()
    {
        return __(ucwords(implode(" ", explode("_", $this->getStatus()))));
    }

    public function getStatus(): string
    {
        return $this->getData(ReturnOrderInterface::STATUS);
    }

    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = [];
        $this->setAddresses(null);
        $this->setItems(null);
    }

    public function unsetData($key = null): ReturnOrderInterface
    {
        parent::unsetData($key);
        if ($key === null) {
            $this->setItems(null);
        }
        return $this;
    }

    public function setItems($items): ReturnOrderInterface
    {
        return $this->setData(self::ITEMS, $items);
    }

    public function getAllItems(): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if (!$item->isDeleted()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @return ReturnOrderItemInterface[]
     */
    public function getItems(): array
    {
        if ($this->getData(self::ITEMS) == null) {
            $this->searchCriteriaBuilder->addFilter(ReturnOrderItemInterface::ORDER_ID, $this->getId());

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $this->setData(
                self::ITEMS,
                $this->itemRepository->getList($searchCriteria)->getItems()
            );
        }
        return $this->getData(self::ITEMS);
    }

    /**
     * Get items collection
     *
     * @param array $filterByTypes
     * @param bool $nonChildrenOnly
     * @return ItemCollection
     */
    public function getItemsCollection(array $filterByTypes = [], bool $nonChildrenOnly = false): ItemCollection
    {
        $collection = $this->_orderItemCollectionFactory->create()->setOrderFilter($this->getEntityId());

        if ($filterByTypes) {
            $collection->filterByTypes($filterByTypes);
        }
        if ($nonChildrenOnly) {
            $collection->filterByParent();
        }

        if ($this->getId()) {
            foreach ($collection as $item) {
                $item->setOrder($this);
            }
        }
        return $collection;
    }

    public function getAllVisibleItems(): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    public function getItemById(int $itemId): ?DataObject
    {
        $items = $this->getItems();

        if (isset($items[$itemId])) {
            return $items[$itemId];
        }

        return null;
    }

    public function addItem(ReturnOrderItemInterface $item): ReturnOrderInterface
    {
        $item->setOrder($this);
        if (!$item->getId()) {
            $this->setItems(array_merge($this->getItems(), [$item]));
        }
        return $this;
    }

    /**
     * Get formatted price value including order currency rate to order website currency
     *
     * @param float $price
     * @param bool $addBrackets
     * @return string
     */
    public function formatPrice($price, $addBrackets = false): string
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * Format price precision
     *
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getOrderCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    public function getOrderCurrency()
    {
        if ($this->_baseCurrency === null) {
            $this->_baseCurrency = $this->_currencyFactory->create()->load($this->getOrderCurrencyCode());
        }
        return $this->_baseCurrency;
    }

    public function getOrderCurrencyCode(): string
    {
        return "EUR";
    }

    public function getAllStatusHistory(): array
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] = $status;
            }
        }
        return $history;
    }

    /**
     * Return collection of order status history items.
     *
     * @return HistoryCollection
     */
    public function getStatusHistoryCollection()
    {
        $collection = $this->_historyCollectionFactory->create()->setOrderFilter($this)
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        if ($this->getId()) {
            foreach ($collection as $status) {
                $status->setOrder($this);
            }
        }
        return $collection;
    }

    public function getStatusHistoryForShopFlix()
    {
        $collection = $this->_historyCollectionFactory->create()->setOrderFilter($this)
            ->addFieldToFilter(StatusHistoryInterface::IS_SHOPFLIX, 1)
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        if ($this->getId()) {
            foreach ($collection as $status) {
                $status->setOrder($this);
            }
        }
        return $collection;
    }

    public function getCustomerName(): string
    {
        return $this->getCustomerFirstname() . " " . $this->getCustomerLastname();
    }

    public function getCustomerFirstname(): string
    {
        return $this->getData(ReturnOrderInterface::CUSTOMER_FIRSTNAME);
    }

    public function getCustomerLastname(): string
    {
        return $this->getData(ReturnOrderInterface::CUSTOMER_LASTNAME);
    }

    public function setBillingAddress(ReturnOrderAddressInterface $billingAddress = null): ReturnOrderInterface
    {
        $old = $this->getBillingAddress();
        if (!empty($old) && !empty($billingAddress)) {
            $billingAddress->setId($old->getId());
        }

        if (!empty($billingAddress)) {
            $billingAddress->setEmail($this->getCustomerEmail());
            $this->addAddress($billingAddress->setAddressType('billing'));
        }
        return $this;
    }

    /**
     * @return ReturnOrderAddressInterface|null
     */
    public function getBillingAddress(): ?ReturnOrderAddressInterface
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getAddressType() == 'billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return null;
    }

    public function getAddresses(): array
    {
        if ($this->getData('addresses') == null) {
            $this->setData(
                'addresses',
                $this->getAddressesCollection()->getItems()
            );
        }
        return $this->getData('addresses');
    }

    /**
     * Get addresses collection
     *
     * @return Collection
     */
    public function getAddressesCollection(): Collection
    {
        $collection = $this->_addressCollectionFactory->create()->setOrderFilter($this);
        if ($this->getId()) {
            foreach ($collection as $address) {
                if (isset($this->regionItems[$address->getCountryId()][$address->getRegion()])) {
                    if ($this->regionItems[$address->getCountryId()][$address->getRegion()]) {
                        $address->setRegion($this->regionItems[$address->getCountryId()][$address->getRegion()]);
                    }
                } else {
                    $region = $this->regionFactory->create();
                    $this->regionResource->loadByName($region, $address->getRegion(), $address->getCountryId());
                    $this->regionItems[$address->getCountryId()][$address->getRegion()] = $region->getName();
                    if ($region->getName()) {
                        $address->setRegion($region->getName());
                    }
                }
                $address->setOrder($this);
            }
        }
        return $collection;
    }

    public function getCustomerEmail(): string
    {
        return $this->getData(ReturnOrderInterface::CUSTOMER_EMAIL);
    }

    public function addAddress(ReturnOrderAddressInterface $address): ReturnOrder
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->setAddresses(array_merge($this->getAddresses(), [$address]));
            $this->setDataChanges(true);
        }
        return $this;
    }

    public function setShippingAddress(ReturnOrderAddressInterface $shippingAddress = null): ReturnOrderInterface
    {
        $old = $this->getShippingAddress();
        if (!empty($old) && !empty($shippingAddress)) {
            $shippingAddress->setId($old->getId());
        }


        if (!empty($shippingAddress)) {
            $shippingAddress->setEmail($this->getCustomerEmail());
            $this->addAddress($shippingAddress->setAddressType('shipping'));
        }
        return $this;
    }

    public function getShippingAddress(): ?ReturnOrderAddressInterface
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getAddressType() == 'shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return null;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function onTheWay(): ReturnOrderInterface
    {
        if ($this->canOnTheWay()) {
            $this->registerOnTheWay();
        }
        return $this;
    }

    public function canOnTheWay(): bool
    {
        if ($this->getStatus() === ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED &&
            $this->getState() === ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX) {
            return true;
        }
        return false;
    }

    public function getState(): ?string
    {
        return $this->getData(ReturnOrderInterface::STATE);
    }

    /**
     * @param bool $graceful
     * @return ReturnOrderInterface
     * @throws LocalizedException
     */
    protected function registerOnTheWay(bool $graceful = true): ReturnOrderInterface
    {
        if ($this->canOnTheWay()) {
            $state = self::STATE_PROCESS_FROM_SHOPFLIX;
            $this->setState($state)
                ->setStatus(ReturnOrderStatusInterface::STATUS_ON_THE_WAY_TO_THE_STORE);
            $this->addStatusHistoryComment(__("Return Order On the way to the store"), ReturnOrderStatusInterface::STATUS_ON_THE_WAY_TO_THE_STORE);

        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot change status for this order.'));
        }
        return $this;
    }

    public function setStatus(string $status): ReturnORderInterface
    {
        return $this->setData(ReturnOrderInterface::STATUS, $status);
    }

    public function setState(string $state): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::STATE, $state);
    }

    /**
     * Add a comment to order.
     *
     * Different or default status may be specified.
     *
     * @param string|Phrase $comment
     * @param bool|string $status
     * @param bool $isShopFlixComment
     * @return ReturnOrderStatusHistoryInterface
     * @see addCommentToStatusHistory
     */
    public function addStatusHistoryComment($comment, $status = false, $isShopFlixComment = false): ReturnOrderStatusHistoryInterface
    {
        return $this->addCommentToStatusHistory($comment, $status, $isShopFlixComment);
    }

    /**
     * @param $comment
     * @param bool|string $status
     * @param bool $isShopFlixComment
     * @return ReturnOrderStatusHistoryInterface
     */
    public function addCommentToStatusHistory($comment, $status = false, $isShopFlixComment = false): ReturnOrderStatusHistoryInterface
    {
        if (false === $status) {
            $status = $this->getStatus();
        } else {
            $this->setStatus($status);
        }
        $history = $this->_orderHistoryFactory->create()
            ->setStatus($status)
            ->setComment($comment)
            ->setIsShopFlix($isShopFlixComment);
        $this->addStatusHistory($history);
        return $history;
    }

    /**
     *
     * Adds the object to the status history collection, which is automatically saved when the order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param History $history
     * @return ReturnOrderInterface
     */
    public function addStatusHistory(History $history): ReturnOrderInterface
    {
        $history->setOrder($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->setStatusHistories(array_merge($this->getStatusHistories(), [$history]));
            $this->setDataChanges(true);
        }
        return $this;
    }

    /**
     * @param array|null $statusHistories
     * @return $this
     */
    public function setStatusHistories(array $statusHistories = null): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::STATUS_HISTORIES, $statusHistories);
    }

    /**
     * @return array|mixed|ReturnOrderStatusHistoryInterface[]|null
     */
    public function getStatusHistories(): ?array
    {
        if ($this->getData(ReturnOrderInterface::STATUS_HISTORIES) == null) {
            $this->setData(
                ReturnOrderInterface::STATUS_HISTORIES,
                $this->getStatusHistoryCollection()->getItems()
            );
        }
        return $this->getData(ReturnOrderInterface::STATUS_HISTORIES);
    }


    public function isDelivered(): bool
    {
        return $this->getStatus() === ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE &&
            $this->getState() === ReturnOrderInterface::STATE_DELIVERED_TO_THE_STORE;
    }

    public function isRequestedReturn(): bool
    {
        return $this->getStatus() == ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED &&
            $this->getState() == ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX;
    }


    /**
     * @return ReturnOrderInterface
     * @throws LocalizedException
     */
    public function decline(): ReturnOrderInterface
    {
        if ($this->canDecline()) {
            $this->registerDecline();
        }
        return $this;
    }

    public function canDecline(): bool
    {
        return $this->isDelivered();
    }

    /**
     * @param bool $graceful
     * @return ReturnOrderInterface
     * @throws LocalizedException
     */
    protected function registerDecline(bool $graceful = true): ReturnOrderInterface
    {
        if ($this->canApprove()) {
            $state = self::STATE_DECLINED;
            $this->setState($state)
                ->setStatus(ReturnOrderStatusInterface::STATUS_RETURN_DECLINED);
            $this->addStatusHistoryComment(__("Return Order declined"), ReturnOrderStatusInterface::STATUS_RETURN_DECLINED);
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot change status for this order.'));
        }
        return $this;
    }

    public function canApprove(): bool
    {
        return $this->isDelivered();
    }

    /**
     * @return ReturnOrderInterface
     * @throws LocalizedException
     */
    public function approved(): ReturnOrderInterface
    {
        if ($this->canApprove()) {
            $this->registerApprove();
        }
        return $this;
    }

    /**
     * @param bool $graceful
     * @return ReturnOrderInterface
     * @throws LocalizedException
     */
    protected function registerApprove(bool $graceful = true): ReturnOrderInterface
    {
        if ($this->canApprove()) {
            $state = self::STATE_APPROVED;
            $this->setState($state)
                ->setStatus(ReturnOrderStatusInterface::STATUS_RETURN_APPROVED);
            $this->addStatusHistoryComment(__("Return Order approved"), ReturnOrderStatusInterface::STATUS_RETURN_APPROVED);
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot change status for this order.'));
        }
        return $this;
    }

    /**
     * @throws LocalizedException
     */
    public function delivered(): ReturnOrderInterface
    {
        if ($this->canDelivered()) {
            $this->registerDelivered();
        }
        return $this;
    }

    public function canDelivered(): bool
    {
        return ($this->getStatus() === ReturnOrderStatusInterface::STATUS_ON_THE_WAY_TO_THE_STORE ||
                $this->getStatus() === ReturnOrderStatusInterface::STATUS_RETURN_REQUESTED) &&
            $this->getState() === ReturnOrderInterface::STATE_PROCESS_FROM_SHOPFLIX;
    }

    /**
     * @param bool $graceful
     * @return ReturnOrderInterface
     * @throws LocalizedException
     */
    protected function registerDelivered(bool $graceful = true): ReturnOrderInterface
    {
        if ($this->canDelivered()) {
            $state = self::STATE_DELIVERED_TO_THE_STORE;
            $this->setState($state)
                ->setStatus(ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE);
            $this->addStatusHistoryComment(__("Return Order delivered to the store"), ReturnOrderStatusInterface::STATUS_DELIVERED_TO_THE_STORE);

        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot change status for this order.'));
        }
        return $this;
    }

    public function getConfig(): Config
    {
        return $this->_orderConfig;
    }

    public function setSynced(bool $synced): ReturnOrderInterface
    {
        return $this->setData(ReturnOrderInterface::SYNC, $synced);
    }

    public function getSynced(): bool
    {
        return $this->getData(ReturnOrderInterface::SYNC);
    }


    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setIdFieldName(ReturnOrderInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }
}
