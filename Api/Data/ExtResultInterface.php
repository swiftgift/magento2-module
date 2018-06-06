<?php
namespace Swiftgift\Gift\Api\Data;
interface ExtResultInterface {
    
    /**
     * @param bool
     * @return $this;
    */
    public function setSuccess($val);

    /**
     * @return bool
     */
    public function getSuccess();

}