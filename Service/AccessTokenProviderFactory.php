<?php
namespace Swiftgift\Gift\Service;

class AccessTokenProviderFactory {

    protected $object_manager;
    protected $config;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $object_manager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->object_manager = $object_manager;
        $this->config = $config;
    }

    public function create() {
        return $this->object_manager->create(
            '\Swiftgift\Gift\Service\AccessTokenProvider',
            [
                'url'=>$this->config->getValue('swiftgift/auth/login_url'),
                'credentials'=>[
                    'email'=>$this->config->getValue('swiftgift/auth/email'),
                    'password'=>$this->config->getValue('swiftgift/auth/password')
                ],
            ]
        );
    }
    
}