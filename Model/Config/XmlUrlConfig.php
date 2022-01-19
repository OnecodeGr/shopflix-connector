<?php
/**
 * XmlUrlConfig.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Url;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Onecode\ShopFlixConnector\Helper\Data;

class XmlUrlConfig implements CommentInterface
{


    private $urlHelper;
    private $helper;
    private $storeManager;
    private $emulation;

    public function __construct(Url $urlHelper, Data $helper, StoreManagerInterface $storeManager, Emulation $emulation)
    {
        $this->urlHelper = $urlHelper;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
    }

    public function getCommentText($elementValue)
    {
        if ($this->helper->canGenerateXml($this->helper->getHashedCode())) {
            $message = __("Please enter the username and password and set to yes to get the link for xml");
            if ($this->helper->getApikey()) {
                $messageData = [];
                foreach ($this->storeManager->getStores() as $store) {
                    if ($store->getIsActive()) {
                        $this->emulation->startEnvironmentEmulation($store->getId(), Area::AREA_FRONTEND, true);
                        $url = $this->urlHelper->getUrl("shopflix/products/feed", ["_query" => ['token' => $this->helper->getHashedCode()]]);

                        $simplerUrl = $this->urlHelper->getUrl("shopflix/products/feed", ["_query" => ['token' => $this->helper->getHashedCode(),"simple"=>1]]);
                        $this->emulation->stopEnvironmentEmulation();
                        $messageData[] = __("<a href='%1' target='_blank'>Xml file for Store View <strong>%2</strong></a>", [$url, $store->getName()]);
                        $messageData[] = __("<a href='%1' target='_blank'>Simple Xml file for Store View <strong>%2</strong></a>", [$simplerUrl, $store->getName()]);
                    }
                }
                $message = implode("<br/>", $messageData);
            }
            return $message;
        }
        return "";
    }
}
