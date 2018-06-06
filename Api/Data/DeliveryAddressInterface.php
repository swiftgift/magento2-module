<?php
namespace Swiftgift\Gift\Api\Data;
interface DeliveryAddressInterface {

    
    /**
     * @param string
     * @return $this;
    */
    public function setFirstName($val);

    /**
     * @return string
     */
    public function getFirstName();


    /**
     * @param string
     * @return $this;
    */
    public function setLastName($val);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string
     * @return $this;
    */
    public function setEmail($val);

    /**
     * @return string
     */
    public function getEmail();
    
    
}