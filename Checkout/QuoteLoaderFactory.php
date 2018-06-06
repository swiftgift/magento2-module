<?php
namespace Swiftgift\Gift\Checkout;

class QuoteLoaderFactory {

    protected $objectManager;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create($isLoggedIn) {
        return $isLoggedIn ? $this->objectManager->create(\Swiftgift\Gift\Checkout\QuoteLoaderStandard::class) : $this->objectManager->create(\Swiftgift\Gift\Checkout\QuoteLoaderGuest::class);
    }
    
}