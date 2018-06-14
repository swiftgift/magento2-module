<?php
namespace Swiftgift\Gift\Service;

class AccessTokenProvider {

    protected $cache;
    protected $client_factory;
    protected $url;
    protected $credentials;

    protected $cache_access_token_id;
    protected $cache_access_token_tag;

    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Swiftgift\Gift\Service\ClientFactory $client_factory,
        $url,
        $credentials,
        $cache_access_token_id='swiftgift_access_token',
        $cache_access_token_tag='SWIFTGIFT_ACCESS_TOKEN'
    ) {
        $this->cache = $cache;
        $this->client_factory = $client_factory;
        $this->url = $url;
        $this->credentials = $credentials;
        $this->cache_access_token_id = $cache_access_token_id;
        $this->cache_access_token_tag = $cache_access_token_tag;
    }

    public function getAccessToken() {
        return $this->cache->load($this->cache_access_token_id);
    }

    public function obtainNewAccessToken() {
        $r = $this->client_factory->create()->request(
            'POST',
            $this->url,
            $this->credentials
        );
        $this->cache->save($r['access_token'], $this->cache_access_token_id, array($this->cache_access_token_tag));
    }
    
}
