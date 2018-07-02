<?php
namespace Swiftgift\Gift\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $context;
    protected $utils;
    protected $scopeConfig;
    
    public function __construct(        
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Swiftgift\Gift\Utils $utils
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->scopeConfig = $scopeConfig;
        $this->utils = $utils;
    }

    public function isConfigValid() {
        return $this->scopeConfig->getValue('swiftgift/main/api_base_url') && $this->scopeConfig->getValue('swiftgift/main/client_secret');
    }

    public function isCanUse() {
        return $this->isConfigValid();
    }
}
