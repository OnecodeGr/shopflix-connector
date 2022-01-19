<?php
/**
 * OfferDateFrom.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class OfferDateFrom extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;
    /**
     * @var TimezoneInterface
     */
    private $localeDate;


    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager      $arrayManager,
        TimezoneInterface $localeDate
    )
    {
        $this->arrayManager = $arrayManager;
        $this->localeDate = $localeDate;
    }


    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta = $this->enableTime($meta);

        return $meta;
    }

    /**
     * Customise Custom Attribute field
     *
     * @param array $meta
     *
     * @return array
     */
    protected function enableTime(array $meta)
    {
        $fieldCode = 'onecode_shopflix_offer_date_from';

        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');


        if (!$elementPath) {
            return $meta;
        }
        $storeTimeZone = $this->localeDate->getConfigTimezone();
        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'children' => [
                    $fieldCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        'dateFormat' => 'yyyy-MM-dd',
                                        'timeFormat' => 'HH:mm',
                                        "storeTimeZone" => $storeTimeZone,
                                        'showsTime' => true,
                                    ]
                                ],
                            ],
                        ],
                    ],

                ]
            ]
        );

        return $meta;
    }
}
