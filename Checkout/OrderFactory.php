<?php
namespace Swiftgift\Gift\Checkout;

class OrderFactory {

    protected $objectManager;
    protected $storeManager;
    protected $config;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config

    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    public function create() {
        return $this->objectManager->create(\Swiftgift\Gift\Checkout\Order::class, [
            'base_url'=>$this->storeManager->getStore()->getBaseUrl(),
            'key_prefix'=>$this->config->getValue('swiftgift/main/idempotency_key_prefix')
        ]);
    }

    
}