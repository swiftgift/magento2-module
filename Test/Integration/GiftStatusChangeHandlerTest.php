<?php
namespace Swiftgift\Gift\Test\Integration;

class GiftStatusChangeHandlerTest extends \Magento\TestFramework\TestCase\AbstractController {

    protected $url_format;

    protected $gift;
    protected $gift_exists_id;
    protected $gift_exists_protect_code_str;
    protected $gift_not_exists_id;
    protected $gift_not_exists_protect_code_str;
    protected $protect_code_invalid_str;
    protected $sg_api_request_data;

    protected $default_status;
    protected $new_status;

    protected $order;

    protected function setUp() {
        parent::setUp();

        $addressData = [
            'region' => 'CA',
            'postcode' => '11111',
            'lastname' => 'lastname',
            'firstname' => 'firstname',
            'street' => 'street',
            'city' => 'Los Angeles',
            'email' => 'admin@example.com',
            'telephone' => '11111111',
            'country_id' => 'US'
        ];

        $objectManager = $this->_objectManager;

        $billingAddress = $objectManager->create(\Magento\Sales\Model\Order\Address::class, ['data' => $addressData]);
        $billingAddress->setAddressType('billing');

        $shippingAddress = clone $billingAddress;
        $shippingAddress->setId(null)->setAddressType('shipping');

        $payment = $objectManager->create(\Magento\Sales\Model\Order\Payment::class);
        $payment->setMethod('checkmo');
        $payment->setAdditionalInformation('last_trans_id', '11122');
        $payment->setAdditionalInformation('metadata', [
            'type' => 'free',
            'fraudulent' => false
        ]);

        /** @var \Magento\Sales\Model\Order $order */
        $order = $objectManager->create(\Magento\Sales\Model\Order::class);

        $order
        ->setState(
            \Magento\Sales\Model\Order::STATE_PROCESSING
        )->setStatus(
            $order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
        )->setSubtotal(
            100
        )->setGrandTotal(
            100
        )->setBaseSubtotal(
            100
        )->setBaseGrandTotal(
            100
        )->setCustomerIsGuest(
            true
        )->setCustomerId(
            null
        )->setCustomerEmail(
            'unknown@example.com'
        )->setBillingAddress(
            $billingAddress
        )->setShippingAddress(
            $shippingAddress
        )->setStoreId(
            $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->getStore()->getId()
        )->setPayment(
            $payment
        );
        $order->isObjectNew(true);
        $order->save();

        $this->order = $order;
        
        
        $this->url_format = "swiftgift/gift/statuschange/gift_id/:gift_id/code/:code";
        $this->gift = $this->_objectManager->create(\Swiftgift\Gift\Model\GiftFactory::class)->create();
        $this->protect_code = $this->_objectManager->create(\Swiftgift\Gift\Service\ProtectCodeFactory::class)->create();
        $this->default_status = 'pending';
        $this->gift->setData([
            'order_id'=>$order->getId(),
            'status'=>$this->default_status,
            'status_change_time'=>time()
        ])->save();

        $this->gift_exists_id = $this->gift->getId();
        $this->gift_exists_protect_code_str = $this->protect_code->generate($this->gift_exists_id);

        $this->gift_not_exists_id = 'gift_not_exist';
        $this->gift_not_exists_protect_code_str = $this->protect_code->generate($this->gift_not_exists_id);

        $this->protect_code_invalid_str = 'protect_code_invalid';

        $this->new_status = 'accepted';

        $this->sg_api_request_data = [
            "id"=> 321,
            "status"=> $this->new_status,
            "delivery_address"=> [
                "country"=> "Great Britain",
                "state"=> "",
                "city"=> "London",
                "postcode"=> "NW1 6XE",
                "street_address1"=> "Baker Street",
                "street_address2"=> "221B",
                "first_name"=> "John",
                "last_name"=> "Watson",
                "phone_number"=>["number"=>9261196296,"prefix"=>7]
            ],
            "reply"=> [
                "text"=> "",
                "image_url"=> ""
            ],
            "reminded"=> null,
            "viewed"=> "2017-02-11T15=>32=>43.103Z",
            "accepted"=> null,
            "dispatched"=> null,
            "replied"=> null,
            "updated"=> "2017-02-11T15=>32=>43.103Z",
            "created"=> "2017-02-11T08=>51=>08.522Z"
        ];
    }

    /**
       @magentoAppArea frontend
     */
    public function testCodeValidGiftExist() {
        $this->checkRequest(
            $this->gift_exists_id,
            $this->gift_exists_protect_code_str,
            200,
            array(
                'success'=>TRUE,
                'errors'=>[]
            )
        );
        $this->checkStatusChanged();
    }
    
    /**
       @magentoAppArea frontend
     */
    public function testCodeInvalidGiftExist() {
        $this->checkRequest(
            $this->gift_exists_id,
            $this->protect_code_invalid_str,
            403,
            array(
                'success'=>FALSE,
                'errors'=>[
                    ['code'=> 'code_not_valid']
                ]
            )
        );
        $this->checkStatusUnChanged();
    }

    /**
       @magentoAppArea frontend
    */
    public function testCodeValidGiftNotExist() {
        $this->checkRequest(
            $this->gift_not_exists_id,
            $this->gift_not_exists_protect_code_str,
            404,
            array(
                'success'=>FALSE,
                'errors'=>[
                    ['code'=> 'gift_not_exists']
                ]
            )
        );
        $this->checkStatusUnChanged();        
    }
    
    /**
       @magentoAppArea frontend
     */
    public function testCodeInvalidGiftNotExist() {
        $this->checkRequest(
            $this->gift_exists_id,
            $this->protect_code_invalid_str,
            403,
            array(
                'success'=>FALSE,
                'errors'=>[
                    ['code'=> 'code_not_valid']
                ]
            )
        );
        $this->checkStatusUnChanged();
    }

    protected function makeUrl($gift_id, $code) {
        return str_replace([':gift_id', ':code'], [$gift_id, $code], $this->url_format);
    }

    protected function checkRequest($gift_id, $code, $expected_response_code, $expected_response_data) {
        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->sg_api_request_data));
        $this->dispatch($this->makeUrl($gift_id, $code));
        $r = $this->getResponse();
        $this->assertEquals($expected_response_code, $r->getStatusCode());
        $this->assertEquals($expected_response_data, json_decode($r->getBody(), TRUE));
    }

    protected function checkStatusChanged() {
        $expected_phone_number = "+{$this->sg_api_request_data['delivery_address']['phone_number']['prefix']}{$this->sg_api_request_data['delivery_address']['phone_number']['number']}";
        $gift = $this->gift->load($this->gift->getId());
        $order = $this->order->load($this->order->getId());
        $this->assertEquals($order->getShippingAddress()->getFirstname(), $this->sg_api_request_data['delivery_address']['first_name']);
        $this->assertEquals($expected_phone_number, $order->getShippingAddress()->getTelephone());
        $this->assertEquals($this->new_status, $gift->getStatus());
    }

    protected function checkStatusUnChanged() {
        $gift = $this->gift->load($this->gift->getId());
        $this->assertEquals($this->default_status, $gift->getStatus());
    }
    
}
