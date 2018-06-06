<?php
namespace Swiftgift\Gift\Checkout\Validator;

class CountryCode extends \Magento\Framework\Validator\AbstractValidator {

    protected $available_countries_codes;

    public function __construct(
        $available_countries_codes
    ) {
        $this->available_countries_codes = $available_countries_codes;
    }

    public function isValid($value) {
        return !$value or count($this->available_countries_codes) === 0 or in_array($value, $this->available_countries_codes, TRUE);
    }
        
}