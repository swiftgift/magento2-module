<?php
namespace Swiftgift\Gift\Plugin;

class CheckoutOrderPluginGuest extends CheckoutOrderPluginBase {

    public function aroundSavePaymentInformationAndPlaceOrder($originObject, $methodClosure, $cartId, $email, $paymentMethod, $billingAddress) {
        $quoteId = $this->quote_id_mask_factory->create()->load($cartId, 'masked_id')->getQuoteId();
        $quote = $this->quote_repository->get($quoteId);
        return $this->handlePlaceOrder($quote, function() use ($methodClosure, $cartId, $email, $paymentMethod, $billingAddress) {
            return $methodClosure($cartId, $email, $paymentMethod, $billingAddress);
        });
    }
    
}
