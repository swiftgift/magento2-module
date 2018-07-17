<?php
namespace Swiftgift\Gift\Controller\Gift;
use \Magento\Framework\App\Action\Action;

class Test extends Action {

    public function execute() {
        $scopeConfig = $this->_objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        var_dump($scopeConfig->getValue('swiftgift/main/api_base_url'));
        exit;
    }    
}
