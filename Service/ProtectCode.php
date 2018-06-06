<?php
namespace Swiftgift\Gift\Service;

class ProtectCode {

    protected $secret_key;

    public function __construct($secret_key) {
        $this->secret_key = $secret_key;
    }

    public function generate($value) {
        return $this->makeCode($this->secret_key, $value);
    }

    public function isValid($protect_code, $value) {
        return $this->makeCode($this->secret_key, $value) === $protect_code;
    }

    protected function makeCode($secret_key, $value) {
        return md5("{$secret_key}{$value}");
    }
    
}