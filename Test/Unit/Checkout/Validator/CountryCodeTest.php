<?php
namespace Swiftgift\Gift\Test\Unit\Checkout\Validator;
use \Swiftgift\Gift\Checkout\Validator;
use PHPUnit\Framework\TestCase;

class CountryCodeTest extends TestCase {

    protected $available_countries_codes;
    protected $country_code_valid;
    protected $country_code_not_valid;

    protected $validator_with_countries_codes;
    protected $validator_without_countries_codes;

    protected function setUp() {
        $this->available_countries_codes = array('gb', 'uk', 'ru', 'usa');
        $this->country_code_valid = 'gb';
        $this->country_code_not_valid = 'notvalid';
        $this->validator_with_countries_codes = new Validator\CountryCode(
            $this->available_countries_codes
        );
        $this->validator_without_countries_codes = new Validator\CountryCode(array());
    }

    public function testValueIsNull() {
        $value = NULL;
        $this->assertEquals(TRUE, $this->validator_with_countries_codes->isValid($value));
        $this->assertEquals(TRUE, $this->validator_without_countries_codes->isValid($value));
    }

    public function testValueValid() {
        $value = $this->country_code_valid;
        $this->assertEquals(TRUE, $this->validator_with_countries_codes->isValid($value));
        $this->assertEquals(TRUE, $this->validator_without_countries_codes->isValid($value));
    }

    public function testValueNotValid() {
        $value = $this->country_code_not_valid;
        $this->assertEquals(FALSE, $this->validator_with_countries_codes->isValid($value));
        $this->assertEquals(TRUE, $this->validator_without_countries_codes->isValid($value));
    }
    
}
