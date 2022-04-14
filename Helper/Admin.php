<?php
/**
 * Admin.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */
declare(strict_types=1);

namespace Onecode\ShopFlixConnector\Helper;

use DOMDocumentFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Onecode\ShopFlixConnector\Model\Order;

class Admin extends AbstractHelper
{

    protected $priceCurrency;
    protected $escaper;
    private $domDocumentFactory;

    /**
     * @param Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param Escaper $escaper
     * @param DOMDocumentFactory|null $domDocumentFactory
     */
    public function __construct(
        Context                $context,
        PriceCurrencyInterface $priceCurrency,
        Escaper                $escaper,
        DOMDocumentFactory     $domDocumentFactory = null
    )
    {
        $this->priceCurrency = $priceCurrency;
        $this->escaper = $escaper;
        $this->domDocumentFactory = $domDocumentFactory
            ?: ObjectManager::getInstance()->get(DOMDocumentFactory::class);
        parent::__construct($context);
    }

    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        if ($dataObject instanceof Order) {
            $order = $dataObject;
        } else {
            $order = $dataObject->getOrder();
        }

        if ($order) {
            $res = $order->formatPrice($price);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        } else {
            $res = $this->priceCurrency->format($price);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        }
        return $res;
    }

    /**
     * Escape string preserving links
     *
     * @param string $data
     * @param array|null $allowedTags
     * @return string
     */
    public function escapeHtmlWithLinks(string $data, array $allowedTags = null): string
    {
        if (!empty($data) && is_array($allowedTags) && in_array('a', $allowedTags)) {
            $wrapperElementId = uniqid();
            $domDocument = $this->domDocumentFactory->create();

            $internalErrors = libxml_use_internal_errors(true);

            $data = mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8');
            $domDocument->loadHTML(
                '<html><body id="' . $wrapperElementId . '">' . $data . '</body></html>'
            );

            libxml_use_internal_errors($internalErrors);

            $linkTags = $domDocument->getElementsByTagName('a');

            foreach ($linkTags as $linkNode) {
                $linkAttributes = [];
                foreach ($linkNode->attributes as $attribute) {
                    $linkAttributes[$attribute->name] = $attribute->value;
                }

                foreach ($linkAttributes as $attributeName => $attributeValue) {
                    if ($attributeName === 'href') {
                        $url = $this->filterUrl($attributeValue ?? '');
                        $url = $this->escaper->escapeUrl($url);
                        $linkNode->setAttribute('href', $url);
                    } else {
                        $linkNode->removeAttribute($attributeName);
                    }
                }
            }

            $result = mb_convert_encoding($domDocument->saveHTML(), 'UTF-8', 'HTML-ENTITIES');
            preg_match('/<body id="' . $wrapperElementId . '">(.+)<\/body><\/html>$/si', $result, $matches);
            $data = !empty($matches) ? $matches[1] : '';
        }

        return $this->escaper->escapeHtml($data, $allowedTags);
    }

    /**
     * Filter the URL for allowed protocols.
     *
     * @param string $url
     * @return string
     */
    private function filterUrl(string $url): string
    {
        if ($url) {
            //Revert the sprintf escaping
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $urlScheme = parse_url($url, PHP_URL_SCHEME);
            $urlScheme = $urlScheme ? strtolower($urlScheme) : '';
            if ($urlScheme !== 'http' && $urlScheme !== 'https') {
                $url = null;
            }
        }

        if (!$url) {
            $url = '#';
        }

        return $url;
    }


}
