<?php
namespace Swiftgift\Gift\Model\Api;

class ExtResult implements \Swiftgift\Gift\Api\Data\ExtResultInterface {

    protected $success;

    

    /**
     * {@inheritdoc}
     */

    public function setSuccess($val) {
        $this->success = $val;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccess() {
        return $this->success;
    }
    
}