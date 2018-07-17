<?php
namespace Swiftgift\Gift\Model\Cache;

class Type extends \Magento\Framework\Cache\Frontend\Decorator\Bare {

  const TYPE_IDENTIFIER = 'swiftgift_access_token';
  const CACHE_TAG = 'SWIFTGIFT_ACCESS_TOKEN';

    public function __construct(
        \Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool
    ) {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
    
}
