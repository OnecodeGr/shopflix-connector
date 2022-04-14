<?php
/**
 * Validator.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Model\Order\Shipment\Track;
use Onecode\ShopFlixConnector\Model\Order\Shipment\Track;

class Validator
{
    /**
     * Required field
     *
     * @var array
     */
    protected $required = [
        'parent_id' => 'Parent Track Id',
        'order_id' => 'Order Id',
        'track_number' => 'Number'
    ];

    /**
     * Validate data
     *
     * @param Track $track
     * @return array
     */
    public function validate(Track $track)
    {
        $errors = [];
        $commentData = $track->getData();
        foreach ($this->required as $code => $label) {
            if (!$track->hasData($code)) {
                $errors[$code] = sprintf('"%s" is required. Enter and try again.', $label);
            } elseif (empty($commentData[$code])) {
                $errors[$code] = sprintf('%s can not be empty', $label);
            }
        }

        return $errors;
    }
}
