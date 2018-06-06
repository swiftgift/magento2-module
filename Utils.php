<?php
namespace Swiftgift\Gift;

class Utils {
    
    public function joinWithTrim($parts, $cm="\\\/", $glue='/') {
        return implode($glue, array_map(function($p) use ($cm) {return trim($p, $cm);}, $parts));
    }

    public function prepareDeliveryAddressData($data) {
        $street_addr_data = array($data['street_address1'], $data['street_address2']);
        $street_addr = implode(" ", array_filter($street_addr_data));
        return [
            'firstname'=>$data['first_name'],
            'lastname'=>$data['last_name'],
            'postcode'=>$data['postcode'],
            'street'=>$street_addr,
            'telephone'=>"+{$data['phone_number']['prefix']}{$data['phone_number']['number']}"
        ];
    }
    
}