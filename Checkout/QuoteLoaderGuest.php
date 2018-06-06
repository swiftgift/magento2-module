<?php
namespace Swiftgift\Gift\Checkout;

class QuoteLoaderGuest {

    protected $quoteIdMaskFactory;
    protected $quoteFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteFactory = $quoteFactory;
    }

    public function load($id) {
        $quote = NULL;
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($id, 'masked_id');
        if ($quoteIdMask->getId() && $quoteIdMask->getQuoteId()) {
            $q = $this->quoteFactory->create()->load($quoteIdMask->getQuoteId());
            if ($q->getId()) {
                $quote = $q;
            }
        }
        return $quote;
    }
    
}