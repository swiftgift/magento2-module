<?php
namespace Swiftgift\Gift\Plugin;

class OrderBlockInfoPlugin {

    public function aroundGetFormattedAddress($objOrigin, $methodClosure, $address) {
        if ($address->getAddressType() === 'swiftgift') {
            return 'Swift gift';
        } else {
            return $methodClosure($address);
        }
    }

}