<?php
/**
 * ReturnReason.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Block\Adminhtml\ReturnOrder\Items\Column;

class ReturnReason extends DefaultColumn
{

    public function getReturnReason(): string
    {
        return $this->getItem()->getReturnReason();
    }

}
