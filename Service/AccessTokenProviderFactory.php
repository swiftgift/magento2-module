<?php
namespace Swiftgift\Gift\Service;

class AccessTokenProviderFactory {

    protected $object_manager;
    protected $config;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $object_manager,
        \Swiftgift\Gift\Utils $utils,        
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->object_manager = $object_manager;
        $this->utils = $utils;
        $this->config = $config;
    }

    public function create() {
        return $this->object_manager->create(
            '\Swiftgift\Gift\Service\AccessTokenProvider',
            [
                'url'=>$this->utils->joinWithTrim([
                    $this->config->getValue('swiftgift/main/api_base_url'),
                    '/v1/auth'
                ]),
                'credentials'=>[
                    'client_secret'=>$this->config->getValue('swiftgift/main/client_secret')
                ],
            ]
        );
    }
    
}
