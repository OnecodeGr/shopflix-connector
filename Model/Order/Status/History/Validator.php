<?php
/**
 * Validator.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Status\History;

use Onecode\ShopFlixConnector\Model\Order\Status\History;

class Validator
{
    /**
     * @var array
     */
    protected $requiredFields = ['parent_id' => 'Order Id'];

    /**
     * @param History $history
     * @return array
     */
    public function validate(History $history)
    {
        $warnings = [];
        foreach ($this->requiredFields as $code => $label) {
            if (!$history->hasData($code)) {
                $warnings[] = sprintf('"%s" is required. Enter and try again.', $label);
            }
        }
        return $warnings;
    }
}
