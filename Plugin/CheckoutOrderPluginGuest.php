<?php
namespace Swiftgift\Gift\Plugin;

class CheckoutOrderPluginGuest extends CheckoutOrderPluginBase {

    public function aroundSavePaymentInformationAndPlaceOrder($originObject, $methodClosure, $cartId, $email, $paymentMethod, $billingAddress) {
        if ($this->helper->isCanUse()) {
            $quoteId = $this->quote_id_mask_factory->create()->load($cartId, 'masked_id')->getQuoteId();
            $quote = $this->quote_repository->get($quoteId);
            if ($quote->getSwiftGiftUsed() === '1') {
                $quote->getShippingAddress()->setCountryId('GB')->save();
                $this->quote_repository->save($quote);
            }
            $orderId = $methodClosure($cartId, $email, $paymentMethod, $billingAddress);
            $order = $this->order_repository->get($orderId);
            $this->handleOrderSave($order, $quote);
            return $orderId;
        } else {
            return $methodClosure($cartId, $email, $paymentMethod, $billingAddress);
        }
    }
    
}
