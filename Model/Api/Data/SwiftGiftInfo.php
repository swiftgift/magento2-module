<?php
namespace Swiftgift\Gift\Model\Api\Data;

class SwiftGiftInfo implements \Swiftgift\Gift\Api\Data\SwiftGiftInfoInterface {

    protected $name;
    protected $message;
    protected $countryCode;
    protected $region;
    protected $regionId;
    protected $shippingMethodCode;
    protected $carrierCode;
    
    /**
       @param string $name
       @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
       @return string
     */
    public function getName() {
        return $this->name;
    }
    

    /**
       @param string $message
       @return $this
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
       @return string
     */
    public function getMessage() {
        return $this->message;
    }
    
    
    /**
       @param string $countryCode
       @return $this
     */
    public function setCountryCode($countryCode) {
        $this->countryCode = $countryCode;
        return $this;
    }


    /**
       @return string
     */
    public function getCountryCode() {
        return $this->countryCode;
    }
    
    
    /**
       @param string $regionId
       @return $this
     */
    public function setRegionId($regionId) {
        $this->regionId = $regionId;
        return $this;
    }

    
    /**
       @return string
     */
    public function getRegionId() {
        return $this->regionId;
    }

    /**
       @return string
     */
    public function getRegion() {
        return $this->region;
    }
    
    
    /**
       @param string $region
       @return $this
     */
    public function setRegion($region) {
        $this->region = $region;
        return $this;
    }


    /**
       @return string
     */
    public function getShippingMethodCode() {
        return $this->shippingMethodCode;
    }
    
    
    /**
       @param string $shippingMethodCode
       @return $this
     */
    public function setShippingMethodCode($shippingMethodCode) {
        $this->shippingMethodCode = $shippingMethodCode;
        return $this;
    }


    /**
       @return string
     */
    public function getCarrierCode() {
        return $this->carrierCode;
    }
    
    
    /**
       @param string $carrierCode
       @return $this
     */
    public function setCarrierCode($carrierCode) {
        $this->carrierCode = $carrierCode;
        return $this;
    }
    
}