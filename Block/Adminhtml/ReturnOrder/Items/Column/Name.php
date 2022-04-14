<?php
/**
 * Name.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\Column;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filter\TruncateFilter\Result;
use Magento\Framework\Registry;

class Name extends DefaultColumn
{
    /**
     * @var Result
     */
    private $truncateResult = null;

    public function __construct(
        Context                     $context,
        StockRegistryInterface      $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry                    $registry, array $data = [],
        ?CatalogHelper              $catalogHelper = null)
    {
        $data['catalogHelper'] = $catalogHelper ?? ObjectManager::getInstance()->get(CatalogHelper::class);
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }

    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption(string $value): array
    {
        $remainder = '';
        $this->truncateString($value, 55, '', $remainder);
        return [
            'value' => nl2br($this->truncateResult->getValue()),
            'remainder' => nl2br($this->truncateResult->getRemainder())
        ];
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function truncateString(string $value, int $length = 80, string $etc = '...', string &$remainder = '', bool $breakWords = true): string
    {
        $this->truncateResult = $this->filterManager->truncateFilter(
            $value,
            ['length' => $length, 'etc' => $etc, 'breakWords' => $breakWords]
        );
        return $this->truncateResult->getValue();
    }
}
