<?php
namespace Swiftgift\Gift\Plugin;

/*['config']['template'] = '';*/

class CheckoutBlockLayoutProcessorPlugin {

    protected $helper;

    public function __construct(
        \Swiftgift\Gift\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function afterProcess($obj, $result) {
        if ($this->helper->isCanUse()) {
            if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street'])) {
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['config']['template'] = 'Swiftgift_Gift/custom/shipping/street-group';
            }
        }
        return $result;
    }
    
}
