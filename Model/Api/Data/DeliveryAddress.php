<?php
namespace Swiftgift\Gift\Model\Api;

class DeliveryAddress implements \Swiftgift\Gift\Api\Data\DeliveryAddressInterface {
    protected $firstName;
    protected $lastName;
    protected $email;



    /**
     * {@inheritdoc}
     */
    public function setFirstName($val) {
        $this->firstName = $val;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */

    public function setLastName($val) {
        $this->lastName = $val;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */

    public function setEmail($val) {
        $this->email = $val;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail() {
        return $this->email;
    }    
    
}