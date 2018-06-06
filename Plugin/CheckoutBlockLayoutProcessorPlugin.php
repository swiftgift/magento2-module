<?php
namespace Swiftgift\Gift\Plugin;

/*['config']['template'] = '';*/

class CheckoutBlockLayoutProcessorPlugin {

    public function afterProcess($obj, $result) {
        if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street'])) {
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['config']['template'] = 'Swiftgift_Gift/custom/shipping/street-group';
        }
        return $result;
    }
    
}