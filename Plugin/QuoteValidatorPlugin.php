<?php
namespace Swiftgift\Gift\Plugin;
use Magento\Framework\Exception\LocalizedException;

class QuoteValidatorPlugin {

    protected $helper;

    public function __construct(
        \Swiftgift\Gift\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function aroundValidateBeforeSubmit($objOrigin, $methodClosure, $quote) {
        if ($this->helper->isCanUse()) {
            if ($quote->getSwiftGiftUsed() === '1') {
                if ($quote->getBillingAddress()->validate() !== true) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __(
                            'Please check the billing address information. %1',
                            implode(' ', $quote->getBillingAddress()->validate())
                        )
                    );
                }
                if (!$quote->getPayment()->getMethod()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please select a valid payment method.'));
                }
                if (!$quote->validateMinimumAmount($quote->getIsMultiShipping())) {
                    throw new LocalizedException($this->minimumAmountMessage->getMessage());
                }
            } else {
                $methodClosure($quote);
            }
        } else {
            return $methodClosure($quote);
        }
    }
    
}
