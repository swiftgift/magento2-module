<?php
namespace Swiftgift\Gift\Api;

interface StatusChangeInterface {

    /**
     * @param string $protect_code
     * @param string $gift_id
     * @param string $status
     * @param \Swiftgift\Gift\Api\Data\DeliveryAddressInterface $delivery_address
     * @return \Swiftgift\Gift\Api\Data\ExtResultInterface
     */

    public function statusChange($protect_code, $gift_id, $status, $delivery_address=null);
    
}