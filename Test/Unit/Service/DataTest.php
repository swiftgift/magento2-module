<?php
namespace Swiftgift\Gift\Test\Unit\Service;
use Magento\Framework\TestFramework\Unit\BaseTestCase;
use \Swiftgift\Gift\Service;
use \Swiftgift\Gift\Exception;

class DataTest extends BaseTestCase {

    protected function createItemMock($media_config, $name, $image_url) {
        $item = $this->createMock(\Magento\Sales\Model\Order\Item::class, [], [], '', false);
        $item->method('getName')->willReturn($name);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class, [], [], '', false);
        $product->method('getImage')->willReturn($image_url);
        $product->method('getMediaConfig')->willReturn($media_config);
        $item->method('getProduct')->willReturn($product);
        return $item;
    }

    public function testCreateGiftData() {
        $key_prefix = 'key_prefix_';
        $order = $this->createMock(\Magento\Sales\Model\Order::class, [], [], '', false);
        $product_img_url_one = 'img/1.jpg';
        $product_img_url_two = 'img/2.jpg';
        $catalog_media_config_url = 'http://domain.com/pub/media/catalog/product';
        $media_config = $this->createMock(\Magento\Catalog\Model\Product\Media\Config::class);
        $media_config->method('getMediaUrl')->willReturnCallback(function($url) use ($catalog_media_config_url) {
            return "{$catalog_media_config_url}/{$url}";
        });
        $order->method('getAllItems')->willReturn(array(
            $this->createItemMock(
                $media_config,
                'Item 1',
                $product_img_url_one
            ),
            $this->createItemMock(
                $media_config,
                'Item 2',
                $product_img_url_two
            ),
        ));
        $order->method('getGrandTotal')->willReturn('36.3900');
        $order->method('getGlobalCurrencyCode')->willReturn('USD');
        $order_id = '123123123';
        $order->method('getId')->willReturn($order_id);

        $billing_addr = $this->createMock(\Magento\Sales\Model\Order\Address::class, [], [], '', false);
        $billing_addr_data = array(
            'lastname'=>'Costello',
            'firstname'=>'Veronica',
        );
        $billing_addr->method('getData')->willReturn($billing_addr_data);
        $order->method('getBillingAddress')->willReturn($billing_addr);

        $sg_form_data = array(
            'name'=>'sgname',
            'country_code'=>'sgcountry',
            'message'=>'sgmsg'
        );
        $validator_factory = $this->createMock(\Swiftgift\Gift\Checkout\ValidatorFactory::class);
        $validator = $this->createMock(\Magento\Framework\Validator::class);
        $validator->method('isValid')->willReturn(true);
        $validator_factory->method('create')->willReturn($validator);
        $api = new Service\Data(
            $validator_factory
        );
        $img_url = '/imgurl';
        $base_url = 'http://domain.com//';
        $this->assertEquals(array(
            'idempotency_key'=>"{$key_prefix}{$order->getId()}",
            'sender'=>array(
                'name'=>$sg_form_data['name'],
                'country'=>$sg_form_data['country_code'],
                'image_url'=>$img_url,
                'billing'=>$billing_addr_data
            ),
            'message'=>array(
                'text'=>$sg_form_data['message'],
                'image_url'=>$img_url
            ),
            'products'=>array(
                array(
                    'name'=>'Item 1',
                    'image_url'=>"{$catalog_media_config_url}/{$product_img_url_one}"
                ),
                array(
                    'name'=>'Item 2',
                    'image_url'=>"{$catalog_media_config_url}/{$product_img_url_two}"
                )
            ),
            'basket_amount'=>'36.3900',
            'currency'=>'USD',
            'delivery'=>array(
                "country"=> $sg_form_data['country_code'],
                "state"=> null,
                "name"=> "DHL Standard Delivery",
                "min_time"=> 1,
                "max_time"=> 2
            ),
        ), $api->createGiftDataFromCheckout(
            $order,
            $sg_form_data,
            array(
                'base_url'=>$base_url,
                'image_url'=>$img_url,
                'key_prefix'=>$key_prefix
            )
        ));
    }

    public function testNotValidSgFormData() {
        $order = $this->createMock(\Magento\Sales\Model\Order::class, [], [], '', false);
        $sg_form_data = array();
        $validator_factory = $this->createMock(\Swiftgift\Gift\Checkout\ValidatorFactory::class);
        $validator = $this->createMock(\Magento\Framework\Validator::class);
        $validator->method('isValid')->willReturn(false);
        $validator_factory->method('create')->willReturn($validator);
        $api = new Service\Data(
            $validator_factory
        );
        $this->expectException(Exception\ServiceException::class);
        $api->createGiftDataFromCheckout(
            $order,
            $sg_form_data,
            array(
                'base_url'=>'http://domain.com//',
                'image_url'=>'/'
            )
        );
    }
    
}
