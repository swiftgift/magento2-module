<?php
namespace Swiftgift\Gift\Plugin;

class CheckoutOrderPluginBase {

    protected $sg_checkout_order_factory;
    protected $order_repository;
    protected $quote_repository;
    protected $quote_id_mask_factory;

    public function __construct(
        \Swiftgift\Gift\Checkout\OrderFactory $sg_checkout_order_factory,
        \Magento\Sales\Model\OrderRepository $order_repository,
        \Magento\Quote\Model\QuoteRepository $quote_repository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quote_id_mask_factory
    ) {
        $this->sg_checkout_order_factory = $sg_checkout_order_factory;
        $this->order_repository = $order_repository;
        $this->quote_repository = $quote_repository;
        $this->quote_id_mask_factory = $quote_id_mask_factory;
    }

    protected function handleOrderSave($order, $quote) {
        if ($quote->getSwiftGiftUsed() === '1') {
            $gift_data = [
                'name'=>$quote->getSwiftGiftName(),
                'message'=>$quote->getSwiftGiftMessage(),
                'country_code'=>$quote->getSwiftGiftCountryCode()
            ];
            $gift = $this->sg_checkout_order_factory->create()->createGiftAfterPlaceOrder(
                $order,
                $gift_data
            );
            $gift->save();
            $order->setSwiftGiftUsed(TRUE);
            $order->getShippingAddress(TRUE)
                ->setCountryId($quote->getSwiftGiftCountryCode())
                ->setRegion($quote->getSwiftGiftRegion())
                ->setRegionId($quote->getSwiftGiftRegionId());
            $this->order_repository->save($order);           
        }
    }
    
}
