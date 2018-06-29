<?php
namespace Swiftgift\Gift\Plugin;

class CheckoutQuotePlugin {

    protected $sg_checkout_quote_factory;
    protected $quote_id_mask_factory;
    protected $quote_repository;
    protected $helper;

    public function __construct(
        \Swiftgift\Gift\Checkout\QuoteFactory $sg_checkout_quote_factory,
        \Magento\Quote\Model\QuoteIdMaskFactory $quote_id_mask_factory,
        \Magento\Quote\Model\QuoteRepository $quote_repository,
        \Swiftgift\Gift\Helper\Data $helper
    ) {
        $this->sg_checkout_quote_factory = $sg_checkout_quote_factory;
        $this->quote_id_mask_factory = $quote_id_mask_factory;
        $this->quote_repository = $quote_repository;
        $this->helper = $helper;
    }

    public function aroundSaveAddressInformation($originObject, $methodClosure, $cart_id, $address_information) {
        $result = $methodClosure($cart_id, $address_information);
        if ($this->helper->isCanUse()) {            
            $quote_id_mask = $this->quote_id_mask_factory->create()->load($cart_id, 'masked_id');
            $quote = $this->quote_repository->get($quote_id_mask->getQuoteId());
            $sg_checkout_quote = $this->sg_checkout_quote_factory->create();
            $sg_checkout_quote->setSwiftGiftNotUsed($quote);
            $this->quote_repository->save($quote);
        }
        return $result;
    }
    
}
