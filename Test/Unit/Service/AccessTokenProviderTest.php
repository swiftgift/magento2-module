<?php
namespace Swiftgift\Gift\Test\Unit\Service;
use PHPUnit\Framework\TestCase;
use Swiftgift\Gift\Service;
use Swiftgift\Gift\Exception;

class AccessTokenProviderTest extends TestCase {

    protected $cache;
    protected $client_factory;
    protected $client;
    protected $url;
    protected $credentials;
    protected $cache_access_token_id = 'cache_access_token_id';
    protected $cache_access_token_tag = 'cache_access_token_tag';

    protected function setUp() {
        $this->cache = $this->createMock(\Magento\Framework\App\CacheInterface::class);
        $this->client_factory = $this->createMock(Service\ClientFactory::class);
        $this->client = $this->createMock(Service\Client::class);
        $this->client_factory->method('create')->willReturn(
            $this->client
        );
        $this->url = 'http://sg.com/auth';
        $this->credentials = array(
            'email'=>'email@email.com',
            'password'=>'password'
        );
    }

    public function testGetAccessToken() {
        $access_token_val = 'access_token_val';
        $this->cache->expects($this->once())->method('load')->with(
            $this->cache_access_token_id
        )->willReturn($access_token_val);
        $provider = new Service\AccessTokenProvider(
            $this->cache,
            $this->client_factory,
            $this->url,
            $this->credentials,
            $this->cache_access_token_id,
            $this->cache_access_token_tag
        );
        $result = $provider->getAccessToken();
        $this->assertEquals($access_token_val, $result);
    }

    public function testObtainNewAccessToken() {
        $access_token_val = 'access_token_val';
        $provider = new Service\AccessTokenProvider(
            $this->cache,
            $this->client_factory,
            $this->url,
            $this->credentials,
            $this->cache_access_token_id,
            $this->cache_access_token_tag
        );
        $this->client->expects($this->once())->method('request')->with(
            'POST',
            $this->url,
            $this->credentials
        )->willReturn(array(
            'auth'=>array(
                'access_token'=>$access_token_val
            )
        ));
        $this->cache->expects($this->once())->method('save')->with(
            $access_token_val,
            $this->cache_access_token_id,
            array($this->cache_access_token_tag)
        );
        $provider->obtainNewAccessToken();
    }
    
}