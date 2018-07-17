<?php
namespace Swiftgift\Gift\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $context;
    protected $utils;
    
    public function __construct(        
        \Magento\Framework\App\Helper\Context $context,
        \Swiftgift\Gift\Utils $utils
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->utils = $utils;
    }

    public function isConfigValid() {
        return $this->context->getScopeConfig()->getValue('swiftgift/main/api_base_url') && $this->context->getScopeConfig()->getValue('swiftgift/main/client_secret');
    }

    public function isCanUse() {
        return $this->isConfigValid();
    }
}
