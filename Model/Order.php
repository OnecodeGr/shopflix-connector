<?php
/**
 * ${FILE_NAME}
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model;

use Magento\Directory\Model\Currency;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region as RegionResource;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use Onecode\ShopFlixConnector\Api\Data\AddressInterface;
use Onecode\ShopFlixConnector\Api\Data\ItemInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusHistoryInterface;
use Onecode\ShopFlixConnector\Api\Data\StatusInterface;
use Onecode\ShopFlixConnector\Model\Order\Config;
use Onecode\ShopFlixConnector\Model\Order\Item;
use Onecode\ShopFlixConnector\Model\Order\ItemRepository;
use Onecode\ShopFlixConnector\Model\Order\Status\History;
use Onecode\ShopFlixConnector\Model\Order\Status\HistoryFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order as ResourceModel;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Address\Collection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Address\CollectionFactory as OrderAddressCollectionFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status\History\Collection as HistoryCollection;
use Onecode\ShopFlixConnector\Model\ResourceModel\Order\Status\History\CollectionFactory as OrderHistoryCollectionFactory;

/**
 *
 * @method setAddresses(AddressInterface[] $array_merge)
 */
class Order extends AbstractModel implements OrderInterface
{

    /**
     * @var string[]
     */
    protected $interfaceAttributes = self::ATTRIBUTES;

    /**
     * @var string
     */
    protected $_eventPrefix = 'onecode_shopflix_order';
    /**
     * @var string
     */
    protected $_eventObject = 'shopflix_order';
    /**
     * @var null
     */
    protected $_baseCurrency = null;
    private $_addressCollectionFactory;
    /**
     * @var RegionFactory|mixed
     */
    private $regionFactory;
    /**
     * @var RegionResource|mixed
     */
    private $regionResource;
    /**
     * @var array
     */
    private $regionItems;
    /**
     * @var SearchCriteriaBuilder|mixed
     */
    private $searchCriteriaBuilder;
    /**
     * @var OrderItemCollectionFactory
     */
    private $_orderItemCollectionFactory;
    /**
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var CurrencyFactory
     */
    private $_currencyFactory;
    /**
     * @var OrderHistoryCollectionFactory
     */
    private $_historyCollectionFactory;
    /**
     * @var HistoryFactory
     */
    private $_orderHistoryFactory;
    /**
     * @var Config
     */
    private $_orderConfig;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param OrderAddressCollectionFactory $addressCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param ItemRepository $itemRepository
     * @param CurrencyFactory $currencyFactory
     * @param HistoryFactory $orderHistoryFactory
     * @param OrderHistoryCollectionFactory $historyCollectionFactory
     * @param Config $config
     * @param OrderRepository $orderRepository
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param RegionFactory|null $regionFactory
     * @param RegionResource|null $regionResource
     * @param SearchCriteriaBuilder|null $searchCriteriaBuilder
     */
    public function __construct(Context                       $context,
                                Registry                      $registry,
                                OrderAddressCollectionFactory $addressCollectionFactory,
                                OrderItemCollectionFactory    $orderItemCollectionFactory,
                                ItemRepository                $itemRepository,
                                CurrencyFactory               $currencyFactory,
                                HistoryFactory                $orderHistoryFactory,
                                OrderHistoryCollectionFactory $historyCollectionFactory,
                                Config                        $config,
                                OrderRepository               $orderRepository,
                                AbstractResource              $resource = null,
                                AbstractDb                    $resourceCollection = null,
                                array                         $data = [],
                                RegionFactory                 $regionFactory = null,
                                RegionResource                $regionResource = null,
                                SearchCriteriaBuilder         $searchCriteriaBuilder = null)
    {
        $this->_addressCollectionFactory = $addressCollectionFactory;
        $this->regionFactory = $regionFactory ?: ObjectManager::getInstance()->get(RegionFactory::class);
        $this->regionResource = $regionResource ?: ObjectManager::getInstance()->get(RegionResource::class);
        $this->regionItems = [];
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_orderHistoryFactory = $orderHistoryFactory;
        $this->_historyCollectionFactory = $historyCollectionFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder ?: ObjectManager::getInstance()
            ->get(SearchCriteriaBuilder::class);
        $this->itemRepository = $itemRepository;
        $this->_orderConfig = $config;
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function getShopFlixOrderId(): int
    {
        return $this->_getData(self::SHOPFLIX_ORDER_ID);
    }

    public function setShopFlixOrderId(int $shopflixOrderId): OrderInterface
    {
        return $this->setData(self::SHOPFLIX_ORDER_ID, $shopflixOrderId);
    }

    public function getSubtotal(): float
    {
        return $this->_getData(self::SUBTOTAL);
    }

    public function setSubtotal(float $subtotal): OrderInterface
    {
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    public function setDiscountAmount(float $discountAmount): OrderInterface
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    public function getTotalPaid(): float
    {
        return $this->_getData(self::TOTAL_PAID);
    }

    public function setTotalPaid(float $totalPaid): OrderInterface
    {
        return $this->setData(self::TOTAL_PAID, $totalPaid);
    }

    public function setCustomerLastname(string $customerLastname): OrderInterface
    {
        return $this->setData(self::CUSTOMER_LASTNAME, $customerLastname);
    }

    public function getCustomerNote(): string
    {
        return $this->_getData(self::CUSTOMER_NOTE);
    }

    public function getDiscountAmount(): float
    {
        return $this->_getData(self::DISCOUNT_AMOUNT);
    }

    public function getIncrementId(): string
    {
        return $this->_getData(self::INCREMENT_ID);
    }

    public function setIncrementId(string $incrementId): OrderInterface
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    public function setCustomerEmail(string $customerEmail): OrderInterface
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    public function setCustomerFirstname(string $customerFirstname): OrderInterface
    {
        return $this->setData(self::CUSTOMER_FIRSTNAME, $customerFirstname);
    }

    public function getRemoteIp(): ?string
    {
        return $this->_getData(self::CUSTOMER_REMOTE_IP);
    }

    public function setRemoteIp(string $remoteIp): OrderInterface
    {
        return $this->setData(self::CUSTOMER_REMOTE_IP, $remoteIp);
    }

    /**
     * @param string $customerNote
     * @return OrderInterface|Order
     */
    public function setCustomerNote(string $customerNote): OrderInterface
    {
        return $this->setData(self::CUSTOMER_NOTE, $customerNote);
    }

    public function setBillingAddress(AddressInterface $billingAddress = null): OrderInterface
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
     * @return AddressInterface|null
     */
    public function getBillingAddress(): ?AddressInterface
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getAddressType() == 'billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return null;
    }

    /**
     * @return AddressInterface []
     */
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
        return $this->_getData(self::CUSTOMER_EMAIL);
    }

    public function addAddress(AddressInterface $address): Order
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->setAddresses(array_merge($this->getAddresses(), [$address]));
            $this->setDataChanges(true);
        }
        return $this;
    }

    /**
     * @param AddressInterface|null $shippingAddress
     * @return $this|mixed
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null): OrderInterface
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

    public function getShippingAddress(): ?AddressInterface
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getAddressType() == 'shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return null;
    }

    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = [];
        $this->setAddresses(null);
        $this->setItems(null);
    }

    public function unsetData($key = null): OrderInterface
    {
        parent::unsetData($key);
        if ($key === null) {
            $this->setItems(null);
        }
        return $this;
    }

    public function setItems($items): OrderInterface
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * Get all items
     *
     * @return \Onecode\ShopFlixConnector\Model\ReturnOrder\Item[]
     */
    public function getAllItems()
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
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        if ($this->getData(self::ITEMS) == null) {
            $this->searchCriteriaBuilder->addFilter(ItemInterface::ORDER_ID, $this->getId());

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

    /**
     * Gets order item by given ID.
     *
     * @param int $itemId
     * @return DataObject|null
     */
    public function getItemById(int $itemId): ?DataObject
    {
        $items = $this->getItems();

        if (isset($items[$itemId])) {
            return $items[$itemId];
        }

        return null;
    }

    /**
     * Add item
     *
     * @param \Onecode\ShopFlixConnector\Model\ReturnOrder\Item $item
     * @return $this
     */
    public function addItem(Item $item): OrderInterface
    {
        $item->setOrder($this);
        if (!$item->getId()) {
            $this->setItems(array_merge($this->getItems(), [$item]));
        }
        return $this;
    }

    public function setBillingAddressId(int $id): OrderInterface
    {
        return $this->setData(
            self::BILLING_ADDRESS_ID,
            $id
        );
    }

    public function getBillingAddressId(): ?int
    {
        return $this->_getData(self::BILLING_ADDRESS_ID);
    }

    public function setShippingAddressId(int $id): OrderInterface
    {
        return $this->setData(
            self::SHIPPING_ADDRESS_ID,
            $id
        );
    }

    public function getShippingAddressId(): ?int
    {
        return $this->_getData(self::SHIPPING_ADDRESS_ID);
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

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return Currency
     */
    public function getOrderCurrency()
    {
        if ($this->_baseCurrency === null) {
            $this->_baseCurrency = $this->_currencyFactory->create()->load($this->getOrderCurrencyCode());
        }
        return $this->_baseCurrency;
    }

    public function getOrderCurrencyCode()
    {
        return "EUR";
    }

    public function getAllStatusHistory()
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

    public function getCustomerName()
    {
        return $this->getCustomerFirstname() . " " . $this->getCustomerLastname();
    }

    public function getCustomerFirstname(): string
    {
        return $this->_getData(self::CUSTOMER_FIRSTNAME);
    }

    public function getCustomerLastname(): string
    {
        return $this->_getData(self::CUSTOMER_LASTNAME);
    }

    public function getStatusLabel()
    {
        return __(ucwords(implode(" ", explode("_", $this->getStatus()))));
    }

    public function getStatus(): ?string
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function accept(bool $synced = false): OrderInterface
    {
        if ($this->canAccept()) {
            $this->registerAcceptance($synced);
        }
        return $this;
    }

    public function canAccept(): bool
    {
        if ($this->isRejected() || $this->isAccepted() || $this->isCompleted()) {
            return false;
        }

        return true;
    }

    public function isRejected()
    {
        return $this->getState() === self::STATE_REJECTED;
    }

    public function getState(): ?string
    {
        return $this->_getData(self::STATE);
    }

    /**
     * Check whether order is accepted
     *
     * @return bool
     */
    public function isAccepted()
    {
        return $this->getState() === self::STATE_ACCEPTED;
    }

    public function isCompleted()
    {
        return $this->getState() === self::STATE_COMPLETED;
    }

    /**
     * @param bool $synced
     * @param bool $graceful
     * @return $this
     * @throws LocalizedException
     */
    protected function registerAcceptance(bool $synced = false, bool $graceful = true): OrderInterface
    {
        if ($this->canAccept()) {
            $state = self::STATE_ACCEPTED;
            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            $this->addStatusHistoryComment(__("Order Accepted"), StatusInterface::STATUS_PICKING);
            $this->setSynced($synced);
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot accept this order.'));
        }
        return $this;
    }

    public function setStatus(string $status): OrderInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setState(string $state): OrderInterface
    {
        return $this->setData(self::STATE, $state);
    }

    public function getConfig(): Config
    {
        return $this->_orderConfig;
    }

    /**
     * Add a comment to order.
     *
     * Different or default status may be specified.
     *
     * @param string|Phrase $comment
     * @param bool|string $status
     * @return StatusHistoryInterface
     * @see addCommentToStatusHistory
     */
    public function addStatusHistoryComment($comment, $status = false, $isShopFlixComment = false): StatusHistoryInterface
    {
        return $this->addCommentToStatusHistory($comment, $status, $isShopFlixComment);
    }

    /**
     * @param $comment
     * @param bool|string $status
     * @return StatusHistoryInterface
     */
    public function addCommentToStatusHistory($comment, $status = false, $isShopFlixComment = false): StatusHistoryInterface
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
     * @return $this
     */
    public function addStatusHistory(History $history): OrderInterface
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
     * @return Order
     */
    public function setStatusHistories(array $statusHistories = null): OrderInterface
    {
        return $this->setData(OrderInterface::STATUS_HISTORIES, $statusHistories);
    }

    /**
     * @return array|mixed|StatusHistoryInterface[]|null
     */
    public function getStatusHistories(): ?array
    {
        if ($this->getData(OrderInterface::STATUS_HISTORIES) == null) {
            $this->setData(
                OrderInterface::STATUS_HISTORIES,
                $this->getStatusHistoryCollection()->getItems()
            );
        }
        return $this->getData(OrderInterface::STATUS_HISTORIES);
    }

    /**
     * @inheridoc
     */
    public function setSynced($sync): OrderInterface
    {
        return $this->setData(OrderInterface::SYNCED, $sync);
    }

    public function canAutoAccept(): bool
    {
        $flag = true;
        foreach ($this->getItems() as $item) {
            $realQty = $item->getRealQty();
            $sum = 0;
            if (is_array($realQty)) {
                foreach ($realQty as $qty) {
                    $sum += $qty['qty'];
                }
            } else {
                $sum = $realQty;
            }
            if ($item->getQty() <= $sum) {
                $flag &= true;
            } else {
                $flag &= false;
            }
        }

        return $flag;
    }


    /**
     * @param string $message
     * @param bool $synced
     * @return $this
     * @throws LocalizedException
     */
    public function reject(string $message = '', $synced = false): OrderInterface
    {
        if ($this->canReject()) {
            $this->registerRejection($message, $synced);
        }
        return $this;
    }

    public function canReject(): bool
    {

        if ($this->isRejected() || $this->isAccepted() || $this->isCompleted()) {
            return false;
        }

        return true;
    }

    /**
     * Prepare order totals to ready to be shipped
     *
     * @param string $comment
     * @param bool synced
     * @param bool $graceful
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function registerRejection(string $comment = '', bool $synced = false, bool $graceful = true): OrderInterface
    {
        if ($this->canReject()) {
            $state = self::STATE_REJECTED;
            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            if (!empty($comment)) {
                $this->addStatusHistoryComment($comment, self::STATE_REJECTED, true);
            }
            $this->setSynced($synced);
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot reject this order.'));
        }
        return $this;
    }

    /**
     * @param string $message
     * @param bool $synced
     * @return $this
     * @throws LocalizedException
     */
    public function readyToBeShipped(string $message = '', bool $synced = false): OrderInterface
    {
        if ($this->canReadyToBeShipped()) {
            $this->registerReadyToBeShipped($synced, $message);
        }
        return $this;
    }

    public function canReadyToBeShipped(): bool
    {
        if ($this->getStatus() !== StatusInterface::STATUS_PICKING) {
            return false;
        }

        return true;
    }

    /**
     * Prepare order totals to cancellation
     *
     * @param bool $synced
     * @param string $comment
     * @param bool $graceful
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function registerReadyToBeShipped(bool $synced = false, bool $graceful = true): OrderInterface
    {
        if ($this->canReadyToBeShipped()) {
            $state = self::STATE_ACCEPTED;
            $this->setState($state)
                ->setStatus(StatusInterface::STATUS_READY_TO_BE_SHIPPED);

            $this->addStatusHistoryComment(__('Ordered is ready to be shipped'),
                StatusInterface::STATUS_READY_TO_BE_SHIPPED
            );

            $this->setSynced($synced);
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot reject this order.'));
        }
        return $this;
    }

    /**
     * @throws LocalizedException
     */
    public function partialShipped(): Order
    {
        if ($this->canPartialShipped()) {
            $this->registerPartialShipped();
        }
        return $this;
    }

    public function canPartialShipped(): bool
    {
        return ($this->getState() === self::STATE_ACCEPTED
                && ($this->getStatus() === StatusInterface::STATUS_ACCEPTED ||
                    $this->getStatus() === StatusInterface::STATUS_PICKING))
            || ($this->getState() === self::STATE_COMPLETED &&
                $this->getStatus() === StatusInterface::STATUS_READY_TO_BE_SHIPPED ||
                $this->getStatus() === StatusInterface::STATUS_ON_THE_WAY);
    }

    protected function registerPartialShipped(string $comment = '', bool $graceful = true): OrderInterface
    {
        if ($this->canPartialShipped()) {
            $state = self::STATE_COMPLETED;
            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            $this->addStatusHistoryComment(__("Order Partial Shipped"), StatusInterface::STATUS_PARTIAL_SHIPPED);

        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot partial shipped this order.'));
        }
        return $this;
    }

    /**
     * @throws LocalizedException
     */
    public function shipped(): Order
    {
        if ($this->canShipped()) {
            $this->registerShipped();
        }
        return $this;
    }

    public function canShipped(): bool
    {
        return ($this->getState() === self::STATE_ACCEPTED
                && ($this->getStatus() === StatusInterface::STATUS_ACCEPTED ||
                    $this->getStatus() === StatusInterface::STATUS_PICKING))
            || ($this->getState() === self::STATE_COMPLETED &&
                ($this->getStatus() === StatusInterface::STATUS_ON_THE_WAY ||
                    $this->getStatus() === StatusInterface::STATUS_READY_TO_BE_SHIPPED ||
                    $this->getStatus() === StatusInterface::STATUS_PARTIAL_SHIPPED));
    }

    protected function registerShipped(string $comment = '', bool $graceful = true): OrderInterface
    {
        if ($this->canShipped()) {
            $state = self::STATE_COMPLETED;
            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            $this->addStatusHistoryComment(__("Order Shipped"), StatusInterface::STATUS_COMPLETED);
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot shipped this order.'));
        }
        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function onTheWay()
    {
        if ($this->canOnTheWay()) {
            $this->registerOnTheWay();
        }
        return $this;
    }

    public function canOnTheWay(): bool
    {
        return ($this->getState() === self::STATE_ACCEPTED
                && ($this->getStatus() === StatusInterface::STATUS_ACCEPTED ||
                    $this->getStatus() === StatusInterface::STATUS_PICKING))
            || ($this->getState() === self::STATE_COMPLETED &&
                $this->getStatus() === StatusInterface::STATUS_READY_TO_BE_SHIPPED);
    }

    protected function registerOnTheWay(string $comment = '', bool $graceful = true): OrderInterface
    {
        if ($this->canOnTheWay()) {
            $state = self::STATE_COMPLETED;
            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            $this->addStatusHistoryComment(__("Order On the way"), StatusInterface::STATUS_ON_THE_WAY);

        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot change status for this order.'));
        }
        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function complete()
    {
        if ($this->canCompleted()) {
            $this->registerCompleted();
        }
        return $this;
    }

    public function canCompleted(): bool
    {
        return ($this->getState() === self::STATE_ACCEPTED
                && ($this->getStatus() === StatusInterface::STATUS_ACCEPTED ||
                    $this->getStatus() === StatusInterface::STATUS_PICKING))
            || ($this->getState() === self::STATE_COMPLETED &&
                ($this->getStatus() === StatusInterface::STATUS_ON_THE_WAY ||
                    $this->getStatus() === StatusInterface::STATUS_READY_TO_BE_SHIPPED ||
                    $this->getStatus() === StatusInterface::STATUS_PARTIAL_SHIPPED ||
                    $this->getStatus() === StatusInterface::STATUS_SHIPPED));
    }

    protected function registerCompleted(string $comment = '', bool $graceful = true): OrderInterface
    {
        if ($this->canCompleted()) {
            $state = self::STATE_COMPLETED;
            $this->setState($state)
                ->setStatus(StatusInterface::STATUS_COMPLETED);
            $this->addStatusHistoryComment(__("Order Completed"), StatusInterface::STATUS_COMPLETED);

        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot complete this order.'));
        }
        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function cancel(): Order
    {
        if ($this->canCancel()) {
            $this->registerCanceled();
        }
        return $this;
    }

    public function canCancel(): bool
    {
        return ($this->getStatus() === StatusInterface::STATUS_PENDING_ACCEPTANCE &&
            $this->getState() === self::STATE_PENDING_ACCEPTANCE);

    }

    protected function registerCanceled(string $comment = '', bool $graceful = true): OrderInterface
    {
        if ($this->canCancel()) {
            $state = self::STATE_CANCELED;
            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            if (!empty($comment)) {
                $this->addStatusHistoryComment($comment, self::STATE_CANCELED);
            }
        } elseif (!$graceful) {
            throw new LocalizedException(__('We cannot cancel this order.'));
        }
        return $this;
    }

    /**
     * Return created_at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(OrderInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt): OrderInterface
    {
        return $this->setData(OrderInterface::CREATED_AT, $createdAt);
    }

    /**
     * Return updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(OrderInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt): OrderInterface
    {
        return $this->setData(OrderInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheridoc
     */
    public function getSynced(): bool
    {
        return $this->_getData(OrderInterface::SYNCED);
    }

    public function setMagnetoOrderId(int $magentoOrderId): OrderInterface
    {
        return $this->setData(OrderInterface::MAGENTO_ORDER_ID, $magentoOrderId);
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getMagentoRealOrder(): \Magento\Sales\Api\Data\OrderInterface
    {
        return $this->orderRepository->get($this->getMagentoOrderId());
    }

    public function getMagentoOrderId(): ?int
    {
        return $this->_getData(OrderInterface::MAGENTO_ORDER_ID);
    }

    public function setIsInvoice(bool $isInvoice): OrderInterface
    {
        return $this->setData(OrderInterface::IS_INVOICE, $isInvoice);
    }

    public function isInvoice(): bool
    {
        return (bool)$this->getData(OrderInterface::IS_INVOICE);
    }

    public function setCompanyName(string $companyName): OrderInterface
    {
        return $this->setData(OrderInterface::COMPANY_NAME, $companyName);
    }

    public function getCompanyName(): ?string
    {
        return $this->getData(OrderInterface::COMPANY_NAME);
    }

    public function setCompanyOwner(string $companyOwner): OrderInterface
    {
        return $this->setData(OrderInterface::COMPANY_OWNER, $companyOwner);
    }

    public function getCompanyOwner(): ?string
    {
        return $this->getData(OrderInterface::COMPANY_OWNER);
    }

    public function setCompanyAddress(string $companyAddress): OrderInterface
    {
        return $this->setData(OrderInterface::COMPANY_ADDRESS, $companyAddress);
    }

    public function getCompanyAddress(): string
    {
        return $this->getData(OrderInterface::COMPANY_ADDRESS);
    }

    public function setCompanyVatNumber(string $companyVatNumber): OrderInterface
    {
        return $this->setData(OrderInterface::COMPANY_VAT_NUMBER, $companyVatNumber);
    }

    public function getCompanyVatNumber(): ?string
    {
        return $this->getData(OrderInterface::COMPANY_VAT_NUMBER);
    }

    public function setTaxOffice(string $taxOffice): OrderInterface
    {
        return $this->setData(OrderInterface::TAX_OFFICE, $taxOffice);
    }

    public function getTaxOffice(): ?string
    {
        return $this->getData(OrderInterface::TAX_OFFICE);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setIdFieldName(OrderInterface::ENTITY_ID);
        $this->_init(ResourceModel::class);
    }

}

