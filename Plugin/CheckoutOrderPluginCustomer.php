<?php
namespace Swiftgift\Gift\Plugin;

class CheckoutOrderPluginCustomer extends CheckoutOrderPluginBase {

    public function aroundSavePaymentInformationAndPlaceOrder($originObject, $methodClosure, $cartId, $paymentMethod, $billingAddress) {
        $quoteId = $cartId;
        $quote = $this->quote_repository->get($quoteId);        
        return $this->handlePlaceOrder($quote, function() use ($methodClosure, $cartId, $paymentMethod, $billingAddress) {
            return $methodClosure($cartId, $paymentMethod, $billingAddress);
        });
    }
    
}
