<?php
namespace Swiftgift\Gift\Checkout;

class Quote {

    protected $sg_validator_factory;

    public function __construct(
        \Swiftgift\Gift\Checkout\ValidatorFactory $sg_validator_factory
    ) {
        $this->sg_validator_factory = $sg_validator_factory;
    }

    protected function _validateSwiftGiftData($data) {
        $v = $this->sg_validator_factory->create();
        return $v->isValid($data) ? TRUE : $v->getMessages();
    }

    public function setSwiftGiftUsed(\Magento\Quote\Model\Quote $quote, $data) {
        $validate_result = $this->_validateSwiftGiftData($data);
        if ($validate_result === true) {
            $addrs = $quote->getAllShippingAddresses();
            if ($addrs) {
                foreach ($addrs as $addr) {
                    $addr->delete();
                }
            }
            $quote->getShippingAddress(TRUE)->setCountryCode($dat['country_code'])->save();
            $quote->addData(array(
                'swift_gift_used'=>TRUE,
                'swift_gift_name'=>$data['name'],
                'swift_gift_message'=>$data['message'],
                'swift_gift_country_code'=>$data['country_code']
            ));
            return true;
        } else {
            return $validate_result;
        }
    }

    public function setSwiftGiftNotUsed(\Magento\Quote\Model\Quote $quote) {
        $quote->addData(array(
            'swift_gift_used'=>FALSE,
            'swift_gift_name'=>NULL,
            'swift_gift_message'=>NULL,
            'swift_gift_country_code'=>NULL
        ));
        return true;
    }
    
}
