<?php
namespace Swiftgift\Gift\Service;

class ProtectCodeFactory {

    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create() {
        $secret_key = $this->objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class)->getValue('swiftgift/auth/secret_key');
        return $this->objectManager->create(ProtectCode::class, [
            'secret_key'=>$secret_key
        ]);        
    }
    
}