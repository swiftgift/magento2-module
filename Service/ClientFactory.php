<?php
namespace Swiftgift\Gift\Service;

class ClientFactory {

    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create() {
        return $this->objectManager->create(\Swiftgift\Gift\Service\Client::class, [
            'http_client_factory'=>$this->objectManager->create(\Magento\Framework\HTTP\ClientFactory::class, [
                'instanceName'=>\Magento\Framework\HTTP\ZendClient::class
            ])
        ]);
    }
    
}
