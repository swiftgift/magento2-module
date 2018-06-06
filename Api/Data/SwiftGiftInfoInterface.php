<?php
namespace Swiftgift\Gift\Api\Data;

interface SwiftGiftInfoInterface {

    /**
       @param string $name
       @return $this
     */
    public function setName($name);


    /**
       @return string
     */
    public function getName();
    

    /**
       @param string $message
       @return $this
     */
    public function setMessage($message);


    /**
       @return string
     */
    public function getMessage();
    
    
    /**
       @param string $countryCode
       @return $this
     */
    public function setCountryCode($countryCode);


    /**
       @return string
     */
    public function getCountryCode();
    
    
    /**
       @param string $regionId
       @return $this
     */
    public function setRegionId($regionId);

    /**
       @return string
     */
    public function getRegionId();    


    /**
       @return string
     */
    public function getRegion();
    
    
    /**
       @param string $region
       @return $this
     */
    public function setRegion($region);


    /**
       @return string
     */
    public function getShippingMethodCode();
    
    
    /**
       @param string $ShippingMethodCode
       @return $this
     */
    public function setShippingMethodCode($ShippingMethodCode);


    /**
       @return string
     */
    public function getCarrierCode();
    
    
    /**
       @param string $carrierCode
       @return $this
     */
    public function setCarrierCode($carrierCode);    
    
}