<?php
namespace Swiftgift\Gift\Checkout;

class OrderFactory {

    protected $objectManager;
    protected $storeManager;
    protected $config;
    protected $deploymentConfig;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig

    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->deploymentConfig = $deploymentConfig;        
    }

    public function create() {
        $base_url = $this->storeManager->getStore()->getBaseUrl();
        return $this->objectManager->create(\Swiftgift\Gift\Checkout\Order::class, [
            'base_url'=>$base_url
        ]);
    }

    
}
