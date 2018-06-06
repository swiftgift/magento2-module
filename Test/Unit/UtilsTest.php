<?php
namespace Swiftgift\Gift\Test\Unit;
use PHPUnit\Framework\TestCase;


class UtilsTest extends TestCase {

    protected $utils;
    protected $delivery_address;

    protected function setUp() {
        $this->utils = new \Swiftgift\Gift\Utils();
        $this->delivery_address = [
            "country"=> "Great Britain",
            "state"=> "",
            "city"=> "London",
            "postcode"=> "NW1 6XE",
            "street_address1"=> "Baker Street",
            "street_address2"=> "221B",
            "first_name"=> "John",
            "last_name"=> "Watson",
            "phone_number"=>["number"=>9261196296,"prefix"=>7]
        ];
    }

    public function testPrepareDeliveryAddressData() {
        $this->assertEquals(
            [
                'firstname'=>$this->delivery_address['first_name'],
                'lastname'=>$this->delivery_address['last_name'],
                'street'=>"{$this->delivery_address['street_address1']} {$this->delivery_address['street_address2']}",
                'postcode'=>$this->delivery_address['postcode'],
                'telephone'=>"+79261196296"
            ],
            $this->utils->prepareDeliveryAddressData($this->delivery_address)
        );
    }
    
}