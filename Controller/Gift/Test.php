<?php
namespace Swiftgift\Gift\Controller\Gift;
use \Magento\Framework\App\Action\Action;

class Test extends Action {

    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        echo $productMetadata->getVersion();
        exit;
    }
    
}
