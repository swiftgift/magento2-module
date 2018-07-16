<?php
namespace Swiftgift\Gift\Block;

class OrderSuccessGift extends \Magento\Framework\View\Element\Template {

    protected $checkoutSession;
    protected $giftModelFactory;
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Swiftgift\Gift\Model\GiftFactory $giftModelFactory,
        \Swiftgift\Gift\Helper\Data $helper
        
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->giftModelFactory = $giftModelFactory;
        $this->helper = $helper;
    }

    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    protected function prepareBlockData()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $gift = $this->giftModelFactory->create()->load($order->getId(), 'order_id');
        if (!$gift->getId()) {
            $gift = null;
        }
        $success = $gift !== null;
        $this->addData([
            'order'=>$order,
            'show'=>$order->getSwiftGiftUsed(),
            'gift'=>$gift,
            'success'=>$success,
            'magic_link_url'=>($gift ? $gift->getShareUrl() : null)
        ]);
    }    
    
}
