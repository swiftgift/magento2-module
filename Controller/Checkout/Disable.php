<?php
namespace Swiftgift\Gift\Controller\Checkout;
use \Magento\Framework\App\Action\Action;

class Disable extends Action {

    protected $quoteLoaderFactory;
    protected $sg_quote;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Swiftgift\Gift\Checkout\QuoteLoaderFactory $quoteLoaderFactory,
        \Swiftgift\Gift\Checkout\Quote $sg_quote
    ) {
        parent::__construct($context);
        $this->quoteLoaderFactory = $quoteLoaderFactory;
        $this->sg_quote = $sg_quote;
    }

    protected function getQuote($cart_id) {
        return $this->quoteLoaderFactory->create()->loadByCartId($cart_id);
    }

    public function execute() {
        $request = $this->getRequest();
        $cart_id = $request->getParam('cart_id');
        $quote = $this->getQuote($cart_id);
        $result = ['success'=>FALSE];
        if ($quote) {
            $_result = $this->sg_quote->setSwiftGiftNotUsed(
                $quote
            );
            if ($_result === true) {
                $quote->save();
                $result['success'] = true;
            } else {
                $result['errors'] = $_result;
            }
        } else {
            $result['errors'] = ['cant load quote'];
        }
        $this->getResponse()->setStatusCode(200)->setBody(json_encode($result));
    }
        
}