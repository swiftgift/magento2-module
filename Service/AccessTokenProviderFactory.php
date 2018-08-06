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

    public function create($base_url=null, $client_secret=null) {
        if ($base_url === null) {
            $base_url = $this->config->getValue('swiftgift/main/api_base_url');
        }
        if ($client_secret === null) {
            $client_secret = $this->config->getValue('swiftgift/main/client_secret');
        }
        $url = $this->utils->joinWithTrim([
            $base_url,
            '/v1/auth'
        ]);
        $credentials = array(
            'client_secret'=>$client_secret
        );
        return $this->object_manager->create(
            '\Swiftgift\Gift\Service\AccessTokenProvider',
            array(
                'url'=>$url,
                'credentials'=>$credentials,
            )
        );
    }
    
}
