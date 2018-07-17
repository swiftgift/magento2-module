<?php
namespace Swiftgift\Gift\Plugin;

class CheckoutOrderPluginCustomer extends CheckoutOrderPluginBase {

    public function aroundSavePaymentInformationAndPlaceOrder($originObject, $methodClosure, $cartId, $paymentMethod, $billingAddress) {
        if ($this->helper->isCanUse()) {
            $quote = $this->quote_repository->get($cartId);
            if ($quote->getSwiftGiftUsed() === '1') {
                //$quote->getShippingAddress()->setCountryId('GB')->save();
                //$this->quote_repository->save($quote);
            }
            $orderId = $methodClosure($cartId, $paymentMethod, $billingAddress);
            $order = $this->order_repository->get($orderId);
            $this->handleOrderSave($order, $quote);
            return $orderId;
        } else {
            return $methodClosure($cartId, $paymentMethod, $billingAddress);
        }
    }
    
}
