<?php
namespace Swiftgift\Gift\Test\Unit\Service;
use \Swiftgift\Gift\Service;
use \PHPUnit\Framework\TestCase;

class ProtectCodeTest extends TestCase {

    protected $secret_key;
    protected $value;
    protected $protect_code_valid;
    protected $protect_code_invalid;
    
    protected $protect_code;

    protected function setUp() {
        $this->secret_key = '123';
        $this->value = '321';
        $this->protect_code_valid = md5("{$this->secret_key}{$this->value}");
        $this->protect_code_invalid = 'invalid';
        $this->protect_code = new Service\ProtectCode($this->secret_key);
    }

    public function testGenerate() {
        $this->assertEquals(
            $this->protect_code_valid,
            $this->protect_code->generate($this->value)
        );
    }

    public function testValid() {
        $this->assertEquals(
            TRUE,
            $this->protect_code->isValid($this->protect_code_valid, $this->value)
        );
    }

    public function testNotValid() {
        $this->assertEquals(
            FALSE,
            $this->protect_code->isValid($this->protect_code_invalid, $this->value)
        );
    }
    
}
