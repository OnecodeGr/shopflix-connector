<?php
/**
 * Method.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */


namespace Onecode\ShopFlixConnector\Model\Carrier;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method as RateResultMethod;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Method extends AbstractCarrier implements CarrierInterface
{
    const CODE = "onecode_shopflix_shipping";
    protected $_code = self::CODE;
    protected $_isFixed = true;
    protected $rateResultFactory;
    protected $rateMethodFactory;
    protected $appState;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param State $appState
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory         $rateErrorFactory,
        LoggerInterface      $logger,
        ResultFactory        $rateResultFactory,
        MethodFactory        $rateMethodFactory,
        State                $appState,
        array                $data = []
    )
    {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->appState = $appState;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
    }

    /**
     * @throws LocalizedException
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active') || !$this->isAdminArea()) {
            return false;
        }

        $result = $this->rateResultFactory->create();
        /** @var RateResultMethod $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice(0.00);
        $method->setCost(0.00);

        $result->append($method);

        return $result;
    }

    /**
     * @throws LocalizedException
     */
    private function isAdminArea(): bool
    {
        if ($this->appState->getAreaCode() === FrontNameResolver::AREA_CODE) {
            return true;
        }
        return false;
    }

    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
