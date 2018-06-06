<?php
namespace Swiftgift\Gift\Exception;
class ServiceException extends \Exception {
    
    protected $data;
    protected $error_code;
    
    public function __construct($error_code, $data=array(), $message=null) {
        $this->error_code = $error_code;
        $this->data = $data;
        parent::__construct($message or $error_code);
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getData() {
        return $this->data;
    }
    
}