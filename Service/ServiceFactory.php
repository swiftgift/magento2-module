<?php
namespace Swiftgift\Gift\Service;

class ServiceFactory {

    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Swiftgift\Gift\Utils $utils
    ) {
        $this->objectManager = $objectManager;
        $this->utils = $utils;
    }

    public function create() {
        $base_url = $this->objectManager->get(
                '\Magento\Framework\App\Config\ScopeConfigInterface'
        )->getValue('swiftgift/main/api_base_url');
        return $this->objectManager->create('\Swiftgift\Gift\Service\Service', [
            'gift_url'=>$this->utils->joinWithTrim([$base_url, '/v1/gifts'])
        ]);
    }
    
}
