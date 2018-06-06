<?php
namespace Swiftgift\Gift\Test\Unit\Service;
use PHPUnit\Framework\TestCase;
use Swiftgift\Gift\Service;
use Swiftgift\Gift\Exception;

class ServiceTest extends TestCase {

    protected $client_factory;
    protected $client;

    protected $access_token_provider_factory;
    protected $access_token_provider;

    protected $gift_url = 'http://gift_url.com/';

    protected function setUp() {
        $this->client_factory = $this->createMock(Service\ClientFactory::class);
        $this->client = $this->createMock(Service\Client::class);
        $this->client_factory->method('create')->willReturn(
            $this->client
        );
        $this->access_token_provider_factory = $this->createMock(Service\AccessTokenProviderFactory::class);
        $this->access_token_provider = $this->createMock(Service\AccessTokenProvider::class);
        $this->access_token_provider_factory->method('create')->willReturn(
            $this->access_token_provider
        );

    }

    public function testWithValidAccessToken() {
        $access_token_val = 'access_token_val';
        $method = 'POST';
        $url = 'url';
        $request_data = array('name'=>'value');
        $response_data = array('respname'=>'respvalue');
        $this->access_token_provider->method('getAccessToken')->willReturn($access_token_val);
        $this->client->expects($this->once())->method('request')->with(
            $method,
            $url,
            $request_data
        )->willReturn($response_data);
        $this->client->expects($this->once())->method('setAccessToken')->with(
            $access_token_val
        );
        $service = new Service\Service(
            $this->client_factory,
            $this->access_token_provider_factory,
            $this->gift_url
        );
        $result = $service->request(
            $method,
            $url,
            $request_data
        );
        $this->assertEquals($response_data, $result);
    }

    public function testWithoutAccessToken() {
        $access_token_val = 'access_token_val';
        $method = 'POST';
        $url = 'url';
        $request_data = array('name'=>'value');
        $response_data = array('respname'=>'respvalue');
        $obtain_new_token_called = false;
        $this->access_token_provider->method('getAccessToken')->will($this->returnCallback(function() use (&$obtain_new_token_called, $access_token_val) {
            return $obtain_new_token_called ? $access_token_val : NULL;
        }));
        $this->access_token_provider->expects($this->once())->method('obtainNewAccessToken')->will($this->returnCallback(function() use (&$obtain_new_token_called) {
            $obtain_new_token_called = true;
        }));
        $this->client->expects($this->once())->method('request')->with(
            $method,
            $url,
            $request_data
        )->willReturn($response_data);
        $this->client->expects($this->once())->method('setAccessToken')->with(
            $access_token_val
        );
        $service = new Service\Service(
            $this->client_factory,
            $this->access_token_provider_factory,
            $this->gift_url
        );
        $result = $service->request(
            $method,
            $url,
            $request_data
        );
        $this->assertEquals($response_data, $result);
    }

    public function testWithForbiddenExceptionAccessTokenExpired() {
        $access_token_val = 'access_token_val';
        $access_token_val_new = 'access_token_val_new';
        $obtain_new_token_called = false;        
        $method = 'POST';
        $url = 'url';
        $request_data = array('name'=>'value');
        $response_data = array('respname'=>'respvalue');
        $this->access_token_provider->method('getAccessToken')->will($this->returnCallback(function() use (&$obtain_new_token_called, $access_token_val, $access_token_val_new) {
            return $obtain_new_token_called ? $access_token_val_new : $access_token_val;
        }));
        $this->client->expects($this->exactly(2))->method('request')->will($this->returnCallback(function() use (&$obtain_new_token_called, $response_data) {
            if ($obtain_new_token_called) {
                return $response_data;
            } else {
                throw new \Swiftgift\Gift\Exception\ServiceException(
                    'access_forbidden',
                    array(
                        'status_code'=>403,
                        'data'=>array()
                    ),
                    'forbidden'
                );
            }
        }));
        $this->access_token_provider->expects($this->once())->method('obtainNewAccessToken')->will($this->returnCallback(function() use (&$obtain_new_token_called) {
            $obtain_new_token_called = true;
        }));
        $this->client->expects($this->exactly(2))->method('setAccessToken')->with(
            $this->callback(function($val) use (&$obtain_new_token_called, $access_token_val, $access_token_val_new){
                return $obtain_new_token_called ? ($val === $access_token_val_new) : ($val === $access_token_val);
            })
        );
        $service = new Service\Service(
            $this->client_factory,
            $this->access_token_provider_factory,
            $this->gift_url
        );
        $result = $service->request(
            $method,
            $url,
            $request_data
        );
        $this->assertEquals($result, $response_data);
    }

    public function testWithForbiddenExceptionPermanent() {
        $method = 'POST';
        $url = 'url';
        $request_data = array('name'=>'value');
        $this->client->expects($this->exactly(2))->method('request')->will(
            $this->throwException((new \Swiftgift\Gift\Exception\ServiceException(
            'access_forbidden',
            array(
                'status_code'=>403,
                'data'=>array()
            ),
            'forbidden'
            )))
        );
        $this->access_token_provider->method('getAccessToken')->willReturn('token');
        $this->expectException(Exception\ServiceException::class);
        $service = new Service\Service(
            $this->client_factory,
            $this->access_token_provider_factory,
            $this->gift_url
        );
        $service->request(
            $method,
            $url,
            $request_data
        );
    }
    
    
}
