<?php
namespace Swiftgift\Gift\Checkout;

class QuoteLoaderStandard {

    protected $quoteFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
    }

    public function load($id) {
        $quote = NULL;
        $q = $this->quoteFactory->create()->load($id);
        if ($q->getId()) {
            $quote = $q;
        }
        return $quote;
    }

    
}
