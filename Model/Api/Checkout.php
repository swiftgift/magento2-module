<?php
namespace Swiftgift\Gift\Model\Api;

class Checkout implements \Swiftgift\Gift\Api\CheckoutInterface {

    protected $quoteLoaderFactory;
    protected $sg_quote;
    protected $payment_method_management;
    protected $cartTotalRepository;
    protected $customerSession;

    public function __construct(
        \Swiftgift\Gift\Checkout\QuoteLoaderFactory $quoteLoaderFactory,
        \Swiftgift\Gift\Checkout\Quote $sg_quote,
        \Magento\Quote\Api\PaymentMethodManagementInterface $payment_method_management,
        \Magento\Quote\Model\Cart\CartTotalRepository $cartTotalRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->quoteLoaderFactory = $quoteLoaderFactory;
        $this->sg_quote = $sg_quote;
        $this->payment_method_management = $payment_method_management;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->customerSession = $customerSession;
    }

    public function enable(
        $cartId,
        \Swiftgift\Gift\Api\Data\SwiftGiftInfoInterface $swiftGiftInfo
    ) {
        $result = new \Swiftgift\Gift\Model\Api\Data\Result();
        $quote = $this->quoteLoaderFactory->create($this->customerSession->isLoggedIn())->load($cartId);
        if ($quote) {
            $data = [
                'name'=>$swiftGiftInfo->getName(),
                'message'=>$swiftGiftInfo->getMessage(),
                'country_code'=>$swiftGiftInfo->getCountryCode(),
                'region_id'=>$swiftGiftInfo->getRegionId(),
                'region'=>$swiftGiftInfo->getRegion(),
                'shipping_method_code'=>$swiftGiftInfo->getShippingMethodCode(),
                'carrier_code'=>$swiftGiftInfo->getCarrierCode()
            ];
            $_result = $this->sg_quote->setSwiftGiftUsed(
                $quote,
                $data
            );
            if ($_result) {
                try {
                    $quote->getShippingAddress()
                        ->setCountryId($data['country_code'])
                        ->setRegion($data['region'])
                        ->setRegionId($data['region_id'])
                        ->setShippingMethod("{$data['carrier_code']}_{$data['shipping_method_code']}")
                        ->setBaseSubtotal($quote->getBaseSubtotal())
                        ->setCollectShippingRates(TRUE)->collectShippingRates()
                        ->save();
                    $quote->setCollectedFlag(FALSE)->collectTotals()->save();
                } catch (\Magento\Framework\Validator\Exception $ex) {
                    $_result = [$ex->getMessage()];
                }
            }
            if ($_result === true) {
                $result->setPaymentMethods($this->payment_method_management->getList($quote->getId()));
                $result->setTotals(
                    $this->cartTotalRepository->get($quote->getId())
                );
                $result->setSuccess(TRUE);
            } else {
                $result->setErrorsMessages($_result);
            }
        } else {
            $result->setErrorsMessages(['Quote not exist']);
        }
        return $result;
    }
    
}
