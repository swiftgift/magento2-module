<?php
namespace Swiftgift\Gift\Test\Integration;

class OrderShippingAddressTest extends \PHPUnit\Framework\TestCase {

    protected $_runCode = '';

    protected $_runScope = 'store';

    protected $_runOptions = [];

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Whether absence of session error messages has to be asserted automatically upon a test completion
     *
     * @var bool
     */
    protected $_assertSessionErrors = false;

    protected $order;

    /**
     * Bootstrap application before any test
     */
    protected function setUp()
    {
        $this->_assertSessionErrors = false;
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_objectManager->removeSharedInstance(\Magento\Framework\App\ResponseInterface::class);
        $this->_objectManager->removeSharedInstance(\Magento\Framework\App\RequestInterface::class);
        $this->order = $this->_objectManager->create(\Magento\Sales\Model\OrderFactory::class)->create()->load(1);
    }

    protected function tearDown()
    {
        $this->_request = null;
        $this->_response = null;
        $this->_objectManager = null;
    }

    /**
       @magentoAppArea frontend
       @magentoDataFixture Magento/Checkout/_files/orders.php
     */
    public function testOrderShippingAddressStandard() {
        $this->assertEquals('0', $this->order->getSwiftGiftUsed());
        $this->assertEquals($this->order->getShippingAddress()->getAddressType(), 'shipping');
    }

    /**
       @magentoAppArea frontend
       @magentoDataFixture Magento/Checkout/_files/orders.php
     */    
    public function testOrderShippingAddressSwiftGift() {
        $this->order->setSwiftGiftUsed('1');
        $this->assertEquals('1', $this->order->getSwiftGiftUsed());
        $this->assertEquals($this->order->getShippingAddress()->getAddressType(), 'swiftgift');
    }

    /**
       @magentoAppArea frontend
       @magentoDataFixture Magento/Checkout/_files/orders.php
     */    
    public function testOrderShippingAddressSwiftGiftForce() {
        $this->order->setSwiftGiftUsed('1');
        $this->assertEquals('1', $this->order->getSwiftGiftUsed());
        $this->assertEquals($this->order->getShippingAddress(TRUE)->getAddressType(), 'shipping');
    }

    /**
       @magentoAppArea adminhtml
       @magentoDataFixture Magento/Checkout/_files/orders.php
    */
    public function testOrderShippingAddressSwiftGiftAdminhtmlArea() {
        $this->order->setSwiftGiftUsed('1');
        $this->assertEquals('1', $this->order->getSwiftGiftUsed());
        $this->assertEquals($this->order->getShippingAddress()->getAddressType(), 'shipping');
    }

    
}