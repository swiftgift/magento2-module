<?php
namespace Swiftgift\Gift\Plugin;

class OrderShippingAddressPlugin {

    protected $addressFactory;

    public function __construct(
        \Magento\Sales\Model\Order\AddressFactory $addressFactory
    ) {
        $this->addressFactory = $addressFactory;
    }

    public function aroundGetShippingAddress($objOrigin, $methodClosure, $force=FALSE) {
        if ($force === TRUE) {
            return $methodClosure();
        } else {
            return ((bool)$objOrigin->getSwiftGiftUsed() ? $this->getSgAddress() : $methodClosure());
        }
    }

    protected function getSgAddress() {
        return $this->addressFactory->create()->setAddressType('swiftgift');
    }
    
}
