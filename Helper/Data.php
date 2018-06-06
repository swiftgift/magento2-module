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

    public function getMagicLinkUrl($code) {
        return $this->utils->joinWithTrim(
            [
                $this->context->getScopeConfig()->getValue('swiftgift/main/magic_link_base_url'),
                $code
            ]
        );
    }
    
}
