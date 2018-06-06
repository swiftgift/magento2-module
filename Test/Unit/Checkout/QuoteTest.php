<?php
namespace Swiftgift\Gift\Test\Unit\Checkout;
use \PHPUnit\Framework\TestCase;
use Swiftgift\Gift\Checkout;

class QuoteTest extends TestCase {

    protected $quote;
    protected $sg_validator_factory;
    protected $validator;
    protected $swift_gift_data_valid;

    protected function setUp() {
        $this->quote = $this->createPartialMock(\Magento\Quote\Model\Quote::class, array(
            'getAllShippingAddresses',
            'addData'
        ));
        $this->sg_validator_factory = $this->createMock(\Swiftgift\Gift\Checkout\ValidatorFactory::class);
        $this->validator = $this->createMock(
            \Magento\Framework\Validator::class
        );
        $this->sg_validator_factory->method('create')->willReturn(
            $this->validator
        );
        $this->swift_gift_data_valid = array(
            'name'=>'Name',
            'message'=>'Message',
            'country_code'=>'GB'
        );
    }

    public function testSetSwiftGiftUsed() {
        $this->validator->method('isValid')->willReturn(true);
        
        $ship_addr = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
        $this->quote->method('getAllShippingAddresses')->willReturn(array(
            $ship_addr
        ));
        $ship_addr->expects($this->once())->method('delete');
        
        $quote_service = new Checkout\Quote(
            $this->sg_validator_factory
        );
        $set_data_expected = array(
            'swift_gift_used'=>TRUE
        );
        foreach ($this->swift_gift_data_valid as $k=>$v) {
            $set_data_expected["swift_gift_{$k}"] = $v;
        }
        $this->quote->expects($this->once())->method('addData')->with(
            $this->equalTo($set_data_expected)
        );
        $result = $quote_service->setSwiftGiftUsed(
            $this->quote,
            $this->swift_gift_data_valid
        );
        $this->assertEquals(TRUE, $result);
    }

    public function testSetSwiftGiftUsedNotValid() {
        $err_msgs = array('errmsgs');
        $this->validator->method('isValid')->willReturn(false);
        $this->validator->method('getMessages')->willReturn($err_msgs);
        $quote_service = new Checkout\Quote(
            $this->sg_validator_factory
        );        
        $result = $quote_service->setSwiftGiftUsed(
            $this->quote,
            array()
        );
        $this->quote->expects($this->never())->method('addData');
        $this->assertEquals($err_msgs, $result);
    }

    public function testSetSwiftGiftNotUsed() {
        $set_data_expected = array(
            'swift_gift_used'=>FALSE
        );
        foreach ($this->swift_gift_data_valid as $k=>$v) {
            $set_data_expected["swift_gift_{$k}"] = NULL;
        }
        $this->quote->expects($this->once())->method('addData')->with($set_data_expected);
        $quote_service = new Checkout\Quote(
            $this->sg_validator_factory
        );
        $quote_service->setSwiftGiftNotUsed($this->quote);
    }
    
}