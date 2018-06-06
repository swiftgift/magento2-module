<?php
namespace Swiftgift\Gift\Test\ApiFunctional;
use Magento\TestFramework\TestCase\WebapiAbstract;

class StatusChangeTest extends WebapiAbstract {

    protected function makeStatusChangeServiceInfo($gift_id, $protected_code) {
        return [
            'rest'=>[
                'resourcePath' => "/V1/swiftgift/status-change/{$protected_code}/{$gift_id}",
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ]
        ];
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    public function testOne() {
        
    }
    
}