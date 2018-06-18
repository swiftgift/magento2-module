<?php
namespace Swiftgift\Gift\Test\Integration;

use \Magento\TestFramework\Helper\Bootstrap;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use \Magento\Checkout\Model\Cart;

class UseInCheckoutTest extends \Magento\TestFramework\TestCase\AbstractController {

    private $sg_data_valid;
    private $sg_data_not_valid_vars;
    private $swift_gift_set_url = '/swiftgift/checkout/save';

    private $sample_addr_data;

    private $form_key;

    private $quoteFactory;
    private $quoteIdMaskFactory;
    private $addressFactory;
    private $giftFactory;

    private $sg_enable_url = 'swiftgift/checkout/enable';
    private $sg_disable_url = 'swiftgift/checkout/disable';
    private $order_success_url = 'checkout/onepage/success';

    private $sg_info;

    protected function setUp() {        
        parent::setUp();
        
        $this->quoteFactory = $this->_objectManager->create(\Magento\Quote\Model\QuoteFactory::class);
        $this->quoteIdMaskFactory = $this->_objectManager->create(\Magento\Quote\Model\QuoteIdMaskFactory::class);
        $this->addressFactory = $this->_objectManager->create(\Magento\Quote\Model\Quote\AddressFactory::class);
        $this->giftFactory = $this->_objectManager->create(\Swiftgift\Gift\Model\GiftFactory::class);

        $this->sg_info = $this->_objectManager->create(\Swiftgift\Gift\Api\Data\SwiftGiftInfoInterface::class);
        $this->sg_info
            ->setName('Sender name')
            ->setMessage('Sender message')
            ->setCountryCode('UA')
            ->setRegionId(NULL)
            ->setRegion('uaregion')
            ->setShippingMethodCode('flatrate')
            ->setCarrierCode('flatrate');
        
        $this->sample_addr_data = [
            'email'=>'email@email.com',
            'firstname'=>'firstname',
            'lastname'=>'lastname',
            'middlename'=>'middlename',
            'street'=>'street',
            'city'=>'Mocsow',
            'country_id'=>'GB',
            'region'=>'Region',
            'postcode'=>'123123',
            'telephone'=>'123123123'
        ];
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoAppArea frontend
     */
    public function testSwiftGiftEnable() {
        $quote = $this->getQuote();
        $this->setSwiftGiftUsed($quote);
        $quote = $quote->load($quote->getId());
        $this->assertEquals('1', $quote->getSwiftGiftUsed());
        $shipping_addr = $quote->getShippingAddress();
        $this->assertEquals(
            $this->sg_info->getCountryCode(),
            $shipping_addr->getCountryId()
        );
        $this->assertEquals(
            $this->sg_info->getRegionId(),
            $shipping_addr->getRegionId()
        );
        $this->assertEquals(
            "{$this->sg_info->getCarrierCode()}_{$this->sg_info->getShippingMethodCode()}",
            $shipping_addr->getShippingMethod()
        );
    }

    protected function checkNoSwiftGiftNoShippingAddress($quote) {
        $ex = null;
        try {
            $this->placeOrderFromQuote($quote);
        } catch (\Magento\Framework\Exception\State\InvalidTransitionException $ex) {
            $ex = $ex;
        }
        $this->assertNotNull($ex);
        $this->assertEquals('Shipping address is not set', $ex->getMessage());
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoAppArea frontend
     */    
    public function testNoSwiftGiftNoShippingAddressGuest() {
        $this->checkNoSwiftGiftNoShippingAddress($this->getQuote());
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoDataFixture Magento/Checkout/_files/customers.php
       @magentoAppArea frontend
     */    
    public function testNoSwiftGiftNoShippingAddressCustomer() {
        $this->checkNoSwiftGiftNoShippingAddress($this->withCustomer($this->getQuote()));
    }

    protected function checkSetSwiftGiftSetShippingAddress($quote) {
        $this->setSwiftGiftUsed($quote);
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quote->getId(), 'quote_id');
        $this->setShippingAddress($quoteIdMask->getMaskedId());
        $quote = $this->quoteFactory->create()->load($quote->getId());
        $order = $this->placeOrderFromQuote($quote);
        $this->checkQuoteStandard($quote->load($quote->getId()));
        $this->assertEquals('0', $quote->getSwiftGiftUsed());
        $this->checkOrderStandard($order);
    }
    
    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoAppArea frontend
     */    

    public function testSetSwiftGiftSetShippingAddressGuest() {
        $this->checkSetSwiftGiftSetShippingAddress($this->getQuote());
    }
    
    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoDataFixture Magento/Checkout/_files/customers.php
       @magentoAppArea frontend
     */
    public function testSetSwiftGiftSetShippingAddressCustomer() {
        $this->checkSetSwiftGiftSetShippingAddress($this->withCustomer($this->getQuote()));
    }

    protected function checkSetShippingAddressSetSwiftGift($quote) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quote->getId(), 'quote_id');
        $this->setShippingAddress($quoteIdMask->getMaskedId());
        $this->setSwiftGiftUsed($quote);
        $quote = $quote->load($quote->getId());
        $order = $this->placeOrderFromQuote($quote);
        $this->checkQuoteSwiftGift($quote);
        $this->checkOrderSwiftGift($order);
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoAppArea frontend
     */    
    public function testSetShippingAddressSetSwiftGiftGuest() {
        $this->checkSetShippingAddressSetSwiftGift($this->getQuote());
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoDataFixture Magento/Checkout/_files/customers.php
       @magentoAppArea frontend
     */    
    public function testSetShippingAddressSetSwiftGiftCustomer() {
        $this->checkSetShippingAddressSetSwiftGift($this->withCustomer($this->getQuote()));
    }

    protected function checkSetSwiftGiftNoShippingAddress($quote) {
        $this->setSwiftGiftUsed($quote);
        $quote = $quote->load($quote->getId());
        $order = $this->placeOrderFromQuote($quote);
        $this->checkQuoteSwiftGift($quote);
        $this->checkOrderSwiftGift($order);
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoAppArea frontend
    */
    public function testSetSwiftGiftNoShippingAddressGuest() {
        $this->checkSetSwiftGiftNoShippingAddress($this->getQuote());
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoDataFixture Magento/Checkout/_files/customers.php
       @magentoAppArea frontend
    */
    public function testSetSwiftGiftNoShippingAddressCustomer() {
        $this->checkSetSwiftGiftNoShippingAddress($this->withCustomer($this->getQuote()));
    }    


    protected function checkSetShippingAddressNoSwiftGift($quote) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quote->getId(), 'quote_id');
        $this->setShippingAddress($quoteIdMask->getMaskedId());
        $order = $this->placeOrderFromQuote($quote);
        $quote = $quote->load($quote->getId());
        $this->checkQuoteStandard($quote);
        $this->checkOrderStandard($order);
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoAppArea frontend
     */
    public function testSetShippingAddressNoSwiftGiftGuest() {
        $this->checkSetShippingAddressNoSwiftGift($this->getQuote());
    }

    /**
       @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
       @magentoDataFixture Magento/Checkout/_files/customers.php
       @magentoAppArea frontend
     */
    public function testSetShippingAddressNoSwiftGiftCustomer() {
        $this->checkSetShippingAddressNoSwiftGift($this->withCustomer($this->getQuote()));
    }    

    protected function getQuote($reserved_order_id='test_order_with_simple_product_without_address') {
        return $this->quoteFactory->create()->load($reserved_order_id, 'reserved_order_id');
    }

    protected function withCustomer($quote, $customer_id=1) {
        $quote->setCustomerEmail('email@email.com');
        $quote->setCustomerId($customer_id);
        $quote->save();
        return $quote;
    }

    protected function setSwiftGiftUsed($quote) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quote->getId(), 'quote_id');
        $cart_id = $quoteIdMask->getMaskedId();
        $checkout_api = $this->_objectManager->create(\Swiftgift\Gift\Api\CheckoutInterface::class);
        $result = $checkout_api->enable(
            $cart_id,
            $this->sg_info
        );
        $this->assertEquals($result->getSuccess(), TRUE);
    }

    protected function placeOrderFromQuote($quote) {
        return $quote->getCustomerId() > 0 ? $this->placeOrderFromQuoteCustomer($quote) : $this->placeOrderFromQuoteGuest($quote);
    }

    protected function placeOrderFromQuoteGuest($quote) {
        $payment_inormation_management = $this->_objectManager->create(
            \Magento\Checkout\Api\GuestPaymentInformationManagementInterface::class
        );
        $payment_method = $this->_objectManager->create(\Magento\Quote\Api\Data\PaymentInterface::class);
        $payment_method->setMethod('checkmo');
        $billing_address = $this->addressFactory->create();
        $billing_address->setQuoteId($quote->getId());
        $billing_address->addData($this->sample_addr_data);
        $quote->setBillingAddress($billing_address);
        $quote->save();
        $cart_id = $this->quoteIdMaskFactory->create()->load($quote->getId(), 'quote_id')->getMaskedId();
        $orderId = $payment_inormation_management->savePaymentInformationAndPlaceOrder(
            $cart_id,
            'email@email.com',
            $payment_method,
            $billing_address
        );
        return $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);        
    }

    protected function placeOrderFromQuoteCustomer($quote) {
        $payment_inormation_management = $this->_objectManager->create(
            \Magento\Checkout\Api\PaymentInformationManagementInterface::class
        );
        $payment_method = $this->_objectManager->create(\Magento\Quote\Api\Data\PaymentInterface::class);
        $payment_method->setMethod('checkmo');
        $billing_address = $this->addressFactory->create();
        $billing_address->setQuoteId($quote->getId());
        $billing_address->addData($this->sample_addr_data);
        $quote->setBillingAddress($billing_address);
        $quote->save();
        $cart_id = $this->quoteIdMaskFactory->create()->load($quote->getId(), 'quote_id')->getMaskedId();
        $orderId = $payment_inormation_management->savePaymentInformationAndPlaceOrder(
            $quote->getId(),
            $payment_method,
            $billing_address
        );
        return $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
    }

    protected function setShippingAddress($cart_id) {
        $shipping_info_api = $this->_objectManager->create(\Magento\Checkout\Api\GuestShippingInformationManagementInterface::class);
        $shipping_address = $this->_objectManager->create(\Magento\Quote\Api\Data\AddressInterface::class);
        $shipping_address->addData($this->sample_addr_data);
        $shipping_address_info = $this->_objectManager->create(\Magento\Checkout\Api\Data\ShippingInformationInterface::class);
        $shipping_address_info->setShippingMethodCode('flatrate');
        $shipping_address_info->setShippingCarrierCode('flatrate');
        $shipping_address_info->setShippingAddress($shipping_address);
        return $shipping_info_api->saveAddressInformation(
            $cart_id,
            $shipping_address_info
        );
    }    

    protected function checkOrderSwiftGift($order) {
        $this->assertNotEmpty($order->getId());
        $this->assertEquals('1', $order->getSwiftGiftUsed());
        $gift = $this->giftFactory->create()->load($order->getId(), 'order_id');
        $this->assertNotEmpty($gift->getId());
        $this->assertEquals($gift->getStatus(), 'pending');
        $this->assertNotEmpty($gift->getCode());
        $shipping_address = $order->getShippingAddress(TRUE);
        $this->assertEquals($this->sg_info->getCountryCode(), $shipping_address->getCountryId());
        $this->assertEquals($this->sg_info->getRegion(), $shipping_address->getRegion());
        $this->assertEquals($this->sg_info->getRegionId(), $shipping_address->getRegionId());
    }

    protected function checkQuoteSwiftGift($quote) {
        $this->assertEquals('1', $quote->getSwiftGiftUsed());
        $this->assertNotEmpty($quote->getSwiftGiftName());
        $this->assertNotEmpty($quote->getSwiftGiftMessage());
    }

    protected function checkOrderStandard($order) {
        $this->assertNotEmpty($order->getId());
        $this->assertEquals('0', $order->getSwiftGiftUsed());
        $gift = $this->giftFactory->create()->load($order->getId(), 'order_id');
        $this->assertEmpty($gift->getId());
        $this->assertNotEmpty($order->getShippingAddress()->getId());
        // $this->dispatch($this->order_success_url);
        // $this->assertNotContains('id="swift-gift"', $this->getResponse()->getBody());
    }    
    
    protected function checkQuoteStandard($quote) {
        $this->assertEquals('0', $quote->getSwiftGiftUsed());
        $this->assertEmpty($quote->getSwiftGiftName());
        $this->assertEmpty($quote->getSwiftGiftMessage());
    }
    
}
