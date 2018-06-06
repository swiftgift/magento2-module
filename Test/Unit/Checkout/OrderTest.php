<?php
namespace Swiftgift\Gift\Test\Unit\Checkout;
use Magento\Framework\TestFramework\Unit\BaseTestCase;

class OrderTest extends BaseTestCase {

    protected function setUp() {
        $this->base_url = 'http://baseurl.com/';
        $this->order = $this->createMock(\Magento\Sales\Model\Order::class);
        $this->utils = new \Swiftgift\Gift\Utils();
        $this->sg_service_factory = $this->createMock(\Swiftgift\Gift\Service\ServiceFactory::class);
        $this->sg_service = $this->createMock(\Swiftgift\Gift\Service\Service::class);
        $this->sg_service_factory->method('create')->willReturn(
            $this->sg_service
        );
        $this->sg_service_data = $this->createMock(
            \Swiftgift\Gift\Service\Data::class
        );
        $this->gift_model_factory = $this->createMock(
            \Swiftgift\Gift\Model\GiftFactory::class
        );
        $this->gift_model = $this->createMock(
            \Swiftgift\Gift\Model\Gift::class
        );
        $this->gift_model_factory->method('create')->willReturn(
            $this->gift_model
        );
        $this->protect_code = $this->createMock(
            \Swiftgift\Gift\Service\ProtectCode::class
        );
        $this->protect_code_factory = $this->createMock(
            \Swiftgift\Gift\Service\ProtectCodeFactory::class
        );
        $this->protect_code_factory->method('create')->willReturn($this->protect_code);
        $this->key_prefix = 'key_prefix_';
    }

    public function testCreateGiftAfterPlaceOrder() {
        $order_id = '1';
        $protect_code = '123';
        $gift_id = 'gift_id';
        $this->protect_code->expects($this->once())->method('generate')->with($gift_id)->willReturn($protect_code);
        $this->gift_model->method('getId')->willReturn($gift_id);
        $sg_service_data_gift_data = array(
            'sgsdaname'=>'value',
            'callback_url'=>"{$this->base_url}swiftgift/gift/statuschange/gift_id/{$gift_id}/code/{$protect_code}"
        );
        $sg_form_data = array('sgformname'=>'sgformvalue');
        $gift_result_data = array(
            'code'=>'gift_result_code'
        );
        $this->order->method('getId')->willReturn($order_id);
        $this->sg_service_data->expects($this->once())->method('createGiftDataFromCheckout')->with(
            $this->order,
            $sg_form_data,
            array(
                'base_url'=>$this->base_url,
                'image_url'=>NULL,
                'key_prefix'=>$this->key_prefix
            )
        )->willReturn($sg_service_data_gift_data);

        $gift_initialized = false;
        $data_before_initialized = array(
            'order_id'=>$order_id,
            'status'=>'initialized'
        );
        $data_after_initialized = array(
            'status'=>'pending',
            'code'=>$gift_result_data['code']
        );        
        $this->sg_service->expects($this->once())->method('createGift')->with($sg_service_data_gift_data)->willReturn($gift_result_data);
        $this->gift_model->method('save')->willReturnCallback(function() use (&$gift_initialized) {
            $gift_initialized = TRUE;
        });
        $this->gift_model->method('addData')->willReturnCallback(function($data) use (&$gift_initialized, $data_before_initialized, $data_after_initialized) {
            if ($gift_initialized) {
                $this->assertEquals($data, $data_after_initialized);
            } else {
                $this->assertEquals($data, $data_before_initialized);
            }
        });
        $sg_checkout_order = new \Swiftgift\Gift\Checkout\Order(
            $this->sg_service_factory,
            $this->sg_service_data,
            $this->gift_model_factory,
            $this->protect_code_factory,
            $this->utils,
            $this->base_url,
            $this->key_prefix
        );
        $sg_checkout_order->createGiftAfterPlaceOrder(
            $this->order,
            $sg_form_data
        );
    }
    
}