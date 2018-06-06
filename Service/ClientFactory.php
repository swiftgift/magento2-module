<?php
namespace Swiftgift\Gift\Service;

class ClientFactory {

    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create() {
        return $this->objectManager->create('\Swiftgift\Gift\Service\Client');
    }
    
}