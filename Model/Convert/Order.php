<?php
/**
 * Order.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Convert;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Area;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Service\ShipmentService;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Onecode\ShopFlixConnector\Api\Data\AddressInterface;
use Onecode\ShopFlixConnector\Api\Data\OrderInterface;
use Onecode\ShopFlixConnector\Gateway\Config\ConfigProvider as PaymentProvider;
use Onecode\ShopFlixConnector\Helper\Shipping;
use Onecode\ShopFlixConnector\Model\Carrier\Method as ShippingMethod;
use Magento\Backend\Model\Session\Quote;

class Order
{
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var QuoteFactory
     */
    private $quote;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Rate
     */
    private $shippingRate;

    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var PaymentProvider
     */
    private $paymentProvider;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var Shipping
     */
    private $shippingHelper;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ShipmentService
     */
    private $shipmentService;
    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    private $magentoOrderConverter;
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;
    private AddressInterfaceFactory $addressFactory;
    private Quote $quoteSession;
    private OrderPaymentRepositoryInterface $paymentRepository;

    public function __construct(
        StoreManagerInterface              $storeManager,
        QuoteFactory                       $quote,
        QuoteManagement                    $quoteManagement,
        QuoteRepository                    $quoteRepository,
        CustomerFactory                    $customerFactory,
        CustomerRepositoryInterface        $customerRepository,
        ProductRepository                  $productRepository,
        Rate                               $shippingRate,
        InvoiceService                     $invoiceService,
        InvoiceRepositoryInterface         $invoiceRepository,
        ShipmentRepositoryInterface        $shipmentRepository,
        Transaction                        $transaction,
        PaymentProvider                    $paymentProvider,
        Emulation                          $emulation,
        Shipping                           $shippingHelper,
        OrderRepositoryInterface           $orderRepository,
        \Magento\Sales\Model\Convert\Order $magentoOrderConverter,
        AddressInterfaceFactory            $addressFactory,
        Quote                              $quoteSession,
        OrderPaymentRepositoryInterface    $paymentRepository
    )
    {
        $this->_storeManager = $storeManager;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->shippingRate = $shippingRate;
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->transaction = $transaction;
        $this->paymentProvider = $paymentProvider;
        $this->emulation = $emulation;
        $this->shippingHelper = $shippingHelper;
        $this->orderRepository = $orderRepository;
        $this->magentoOrderConverter = $magentoOrderConverter;
        $this->addressFactory = $addressFactory;
        $this->quoteSession = $quoteSession;
        $this->paymentRepository = $paymentRepository;

    }


    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function toMagentoOrder(OrderInterface $order)
    {
        $store = $this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $this->emulation->startEnvironmentEmulation($store->getId(), Area::AREA_ADMINHTML);

        try {
            $customer = $this->customerRepository->get($order->getCustomerEmail());
        } catch (NoSuchEntityException $e) {
            $customer = $this->customerFactory->create();
            $customer
                ->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($order->getCustomerFirstname())
                ->setLastname($order->getCustomerLastname())
                ->setEmail($order->getCustomerEmail());
            $customer->save();///
            $customer = $this->customerRepository->getById($customer->getEntityId());

        }


        //CREATE QUOTE//
        $quote = $this->quote->create();
        $quote->setStore($store);
        $quote->setCurrency();
        #$quote->assignCustomer($customer);
        foreach ($order->getItems() as $item) {
            $product = $this->productRepository->get($item->getSku());
            $product->setPrice($item->getPrice());
            $quote->addProduct(
                $product,
                intval($item->getQty())
            );
        }


        foreach ($quote->getAllVisibleItems() as $item) {
            $item->setNoDiscount(1);
        }

        $billing = $this->convertAddressToMagentoAddress($order->getBillingAddress(), "billing");
        $shipping = $this->convertAddressToMagentoAddress($order->getShippingAddress(), "shipping");

        $quote->assignCustomerWithAddressChange($customer, $billing, $shipping);

        $shippingDescription = $this->shippingHelper->getShippingConfig('carriers/onecode_shopflix_shipping/title', $store->getId());
        $this->shippingRate
            ->setCode(ShippingMethod::CODE)
            ->setPrice(0.0)
            ->setMethodDescription($shippingDescription);

        $quote->setPaymentMethod(PaymentProvider::CODE);
        #dd($quote->);
        $this->quoteRepository->save($quote);
        $quote->getPayment()->importData([
                PaymentInterface::KEY_METHOD => PaymentProvider::CODE,
                PaymentInterface::KEY_ADDITIONAL_DATA => [
                    "method_title" => $this->paymentProvider->getMethodTitle()
                ]
            ]
        );
        $this->quoteSession->setQuoteId($quote->getId())
            ->setStoreId($store->getId())
            ->setCustomerId($customer->getId());

        #dd($quote->getShippingAddress()->getData());
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingDescription($shippingDescription)
            ->setShippingMethod(ShippingMethod::CODE);
        $quote->getShippingAddress()->addShippingRate($this->shippingRate);
        $quote->setInventoryProcessed(true);
        $quote->setAppliedRuleIds("");
        $this->quoteRepository->save($quote);

        /** @var \Magento\Sales\Model\Order $magentoOrder */
        $magentoOrder = $this->quoteManagement->submit($quote);
        $magentoOrder->setEmailSent(0);
        $magentoOrder->setData("shipping_description", $shippingDescription);
       $payment =  $magentoOrder->getPayment()
            ->setAdditionalInformation(["method_title" => $this->paymentProvider->getMethodTitle()]);
        $this->paymentRepository->save($payment);
        $this->orderRepository->save($magentoOrder);

        $this->invoice($magentoOrder);


        if ($magentoOrder->getEntityId()) {
            $order->setMagnetoOrderId($magentoOrder->getEntityId());
        }
        $this->emulation->stopEnvironmentEmulation();

    }

    /**
     * @param AddressInterface $address
     * @param string $addressType
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    private function convertAddressToMagentoAddress(AddressInterface $address, string $addressType)
    {

        $addressData = [];
        foreach (AddressInterface::ATTRIBUTES as $attribute) {
            $addressData[$attribute] = (string)$address->getData($attribute);
        }
        if (empty((string)$addressData['telephone'])) {
            $addressData['telephone'] = '00000000';
        }
        if (empty($address['region'])) {
            $addressData['region'] = $addressData['city'];
        }

        $addressData['regionId'] = "";
        $addressData['address_type'] = $addressType;
        $addressData['save_in_address_book'] = 1;
        return $this->addressFactory->create()->addData($addressData);
    }

    /**
     * @param \Magento\Sales\Model\Order|\Magento\Sales\Api\Data\OrderInterface $magentoOrder
     * @return void
     * @throws LocalizedException
     */
    public function invoice($magentoOrder)
    {
        if ($magentoOrder->canInvoice() &&
            $this->paymentProvider->getMethodDefaultStatus()
            === \Magento\Sales\Model\Order::STATE_PROCESSING) {

            $magentoOrder->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);

            $invoice = $this->invoiceService->prepareInvoice($magentoOrder);
            $invoice->register();

            $this->invoiceRepository->save($invoice);
            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();

        }
    }

    /**
     * @param \Magento\Sales\Model\Order|\Magento\Sales\Api\Data\OrderInterface $magentoOrder
     * @return void
     * @throws LocalizedException
     */
    public function ship($magentoOrder)
    {
        if ($magentoOrder->canShip()) {
            $shipment = $this->magentoOrderConverter->toShipment($magentoOrder);
            foreach ($magentoOrder->getAllItems() as $orderItem) {
                // Check if order item has qty to ship or is virtual
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }

                $qtyShipped = $orderItem->getQtyToShip();

                // Create shipment item with qty
                $shipmentItem = $this->magentoOrderConverter->itemToShipmentItem($orderItem)
                    ->setQty($qtyShipped);

                // Add shipment item to shipment
                $shipment->addItem($shipmentItem);
            }
            $shipment->register();

            $shipment->getOrder()->setState(\Magento\Sales\Model\Order::STATE_COMPLETE)
                ->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);

            $this->shipmentRepository->save($shipment);
            $transactionSave = $this->transaction->addObject(
                $shipment
            )->addObject(
                $shipment->getOrder()
            );
            $transactionSave->save();

        }


    }
}
