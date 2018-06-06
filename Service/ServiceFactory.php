<?php
namespace Swiftgift\Gift\Service;

class ServiceFactory {

    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create() {
        return $this->objectManager->create('\Swiftgift\Gift\Service\Service', [
            'gift_url'=>$this->objectManager->get(
                '\Magento\Framework\App\Config\ScopeConfigInterface'
            )->getValue('swiftgift/main/gift_url')
        ]);
    }
    
}