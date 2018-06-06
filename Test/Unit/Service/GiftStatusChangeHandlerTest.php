<?php
namespace Swiftgift\Gift\Test\Unit\Service;
use PHPUnit\Framework\TestCase;

class GiftStatusChangeHandlerTest extends TestCase {

    protected $gift_factory;
    protected $gift;

    protected $protect_code_factory;
    protected $protect_code;

    protected $gift_id;
    protected $protect_code_str;

    protected $gift_exists;
    protected $gift_not_exists;

    protected $new_status;

    protected function setUp() {

        $this->logger = $this->createMock(\Magento\Framework\Logger\Monolog::class);
        
        $this->gift_factory = $this->createMock(\Swiftgift\Gift\Model\GiftFactory::class);
        $this->gift = $this->createMock(\Swiftgift\Gift\Model\Gift::class);
        $this->gift_factory->method('create')->willReturn($this->gift);

        $this->order_exists = $this->createMock(\Magento\Sales\Model\Order::class);
        $this->order_exists->method('getId')->willReturn(1);

        $this->order_not_exists = $this->createMock(\Magento\Sales\Model\Order::class);
        $this->order_not_exists->method('getId')->willReturn(NULL);
        

        $this->order = $this->createMock(\Magento\Sales\Model\Order::class);
        $this->order_factory = $this->createMock(\Magento\Sales\Model\OrderFactory::class);
        $this->order_factory->method('create')->willReturn($this->order);

        $this->order_not_exist = $this->createMock(\Magento\Sales\Model\Order::class);
        $this->order_not_exist->method('load')->willReturn($this->order_not_exist);
        $this->order_not_exist->method('getId')->willReturn(NULL);


        $this->order_shipping_address = $this->createMock(\Magento\Sales\Model\Order\Address::class);
        $this->order_shipping_address->method('addData')->willReturn($this->order_shipping_address);
        $this->order->method('getShippingAddress')->willReturn($this->order_shipping_address);

        $this->protect_code_factory = $this->createMock(\Swiftgift\Gift\Service\ProtectCodeFactory::class);
        $this->protect_code = $this->createMock(\Swiftgift\Gift\Service\ProtectCode::class);
        $this->protect_code_factory->method('create')->willReturn($this->protect_code);

        $this->gift_id = 'gift_id';
        $this->protect_code_str = 'protect_code_str';

        $this->gift_exists = $this->createMock(\Swiftgift\Gift\Model\Gift::class);
        $this->gift_exists->method('setNewStatus')->willReturn($this->gift_exists);
        $this->gift_not_exists = $this->createMock(\Swiftgift\Gift\Model\Gift::class);

        $this->gift_exists->method('getId')->willReturn($this->gift_id);
        $this->gift_not_exists->method('getId')->willReturn(NULL);

        $this->gift_status_change_handler = new \Swiftgift\Gift\Service\GiftStatusChangeHandler(
            $this->gift_factory,
            $this->order_factory,            
            $this->protect_code_factory,
            $this->logger
        );
        $this->delivery_address = [
            'first_name'=>'First name',
            'last_name'=>'Last name'
        ];
    }

    public function testCodeValidGiftExistStatusAccepted() {
        $status_new = \Swiftgift\Gift\Service\GiftStatusChangeHandler::STATUS_COMPLETE;
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(TRUE);
        $this->gift->expects($this->once())->method('load')->with($this->gift_id)->willReturn($this->gift_exists);
        $this->gift_exists->expects($this->once())->method('setNewStatus')->with($this->callback(function($status) use ($status_new) {
            return $status_new === $status;
        }));
        $this->gift_exists->expects($this->once())->method('save');
        $this->order_shipping_address->expects($this->once())->method('addData')->with($this->delivery_address);
        $this->order_shipping_address->expects($this->once())->method('save');
        $this->order->method('load')->willReturn($this->order_exists);
        $this->order_exists->method('getShippingAddress')->willReturn($this->order_shipping_address);
        $gift = $this->gift_status_change_handler->handle(
            $this->gift_id,
            $this->protect_code_str,
            $status_new,
            $this->delivery_address
        );
        $this->assertEquals($this->gift_exists, $gift);
    }

    public function testCodeValidGiftExistStatusAcceptedWithoutDeliverAddress() {
        $status_new = \Swiftgift\Gift\Service\GiftStatusChangeHandler::STATUS_COMPLETE;
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(TRUE);
        $this->gift->expects($this->once())->method('load')->with($this->gift_id)->willReturn($this->gift_exists);
        $this->gift_exists->expects($this->never())->method('save');
        $ex = null;
        try {
            $this->gift_status_change_handler->handle(
                $this->gift_id,
                $this->protect_code_str,
                $status_new,
                null
            );
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            $ex = $ex;
        }
        $this->assertEquals('delivery_address_empty', $ex->getErrorCode());
    }

    public function testCodeValidGiftExistStatusAcceptedOrderNotExists() {
        $status_new = \Swiftgift\Gift\Service\GiftStatusChangeHandler::STATUS_COMPLETE;
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(TRUE);
        $this->gift->expects($this->once())->method('load')->with($this->gift_id)->willReturn($this->gift_exists);
        $this->gift_exists->expects($this->never())->method('save');
        $this->order->method('load')->willReturn($this->order_not_exist);
        $ex = null;
        try {
            $this->gift_status_change_handler->handle(
                $this->gift_id,
                $this->protect_code_str,
                $status_new,
                $this->delivery_address
            );
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            $ex = $ex;
        }
        $this->assertEquals('order_not_exists', $ex->getErrorCode());
    }    

    public function testCodeValidGiftExistStatusOther() {
        $status_new = 'other';
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(TRUE) ;
        $this->gift->expects($this->once())->method('load')->with($this->gift_id)->willReturn($this->gift_exists);
        $this->gift_exists->expects($this->once())->method('setNewStatus')->with($this->callback(function($status) use ($status_new) {
            return $status === $status_new;
        }));
        $this->gift_exists->expects($this->once())->method('save');
        $this->order_shipping_address->expects($this->never())->method('addData')->with($this->delivery_address);
        $this->order_shipping_address->expects($this->never())->method('save');
        $gift = $this->gift_status_change_handler->handle(
            $this->gift_id,
            $this->protect_code_str,
            $status_new,
            $this->delivery_address
        );
        $this->assertEquals($this->gift_exists, $gift);
    }

    public function testCodeInvalidGiftExist() {
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(FALSE);        
        $ex = null;
        try {
            $this->gift_status_change_handler->handle(
                $this->gift_id,
                $this->protect_code_str,
                'other'
            );
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            $ex = $ex;
        }
        $this->assertNotNull($ex);
        $this->assertEquals('code_not_valid', $ex->getErrorCode());
    }
    
    public function testCodeValidGiftNotExist() {
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(TRUE);
        $this->gift->expects($this->once())->method('load')->with($this->gift_id)->willReturn($this->gift_not_exists);
        $ex = null;
        try {
            $this->gift_status_change_handler->handle(
                $this->gift_id,
                $this->protect_code_str,
                'other'
            );
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            $ex = $ex;
        }
        $this->assertNotNull($ex);
        $this->assertEquals('gift_not_exists', $ex->getErrorCode());
    }

    public function testCodeInvalidGiftNotExist() {
        $this->protect_code->expects($this->once())->method('isValid')->with($this->protect_code_str)->willReturn(FALSE);
        $this->gift->method('load')->willReturn($this->gift_not_exists);
        $ex = null;
        try {
            $this->gift_status_change_handler->handle(
                $this->gift_id,
                $this->protect_code_str,
                'other'
            );
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            $ex = $ex;
        }
        $this->assertNotNull($ex);
        $this->assertEquals('code_not_valid', $ex->getErrorCode());        
    }
    
}