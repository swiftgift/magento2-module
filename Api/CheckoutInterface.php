<?php
namespace Swiftgift\Gift\Api;

interface CheckoutInterface {

    /**
     * @param string $cartId
     * @param \Swiftgift\Gift\Api\Data\SwiftGiftInfoInterface $swiftGiftInfo
     * @return \Swiftgift\Gift\Api\Data\ResultInterface
     */
    
    public function enable(
        $cartId,
        \Swiftgift\Gift\Api\Data\SwiftGiftInfoInterface $swiftGiftInfo
    );
    
}