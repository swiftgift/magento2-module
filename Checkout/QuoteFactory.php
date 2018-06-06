<?php
namespace Swiftgift\Gift\Checkout;

class QuoteFactory {

    protected $objectManager;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create() {
        return $this->objectManager->create(\Swiftgift\Gift\Checkout\Quote::class);
    }

    
}