<?php
namespace Swiftgift\Gift\Test\Unit\Service;
use PHPUnit\Framework\TestCase;
use Swiftgift\Gift\Service;
use Swiftgift\Gift\Exception;


class ClientTest extends TestCase {

    protected $http_client_factory;
    protected $http_client;
    protected $request_sample_data;
    protected $response_sample_data;

    protected function setUp() {
        $this->http_client_factory = $this->createMock(\Magento\Framework\HTTP\ClientFactory::class);
        $this->http_client = $this->createMock(\Magento\Framework\HTTP\ClientInterface::class);
        $this->http_client_factory->method('create')->willReturn(
            $this->http_client
        );
        $this->resource_url = 'http://domain.com/public/gifts';
        $this->request_sample_data = array(
            'field'=>'value'
        );
        $this->response_sample_data = array(
            'field'=>'value'
        );
    }

    protected function checkValidRequest($response_status) {
        $this->http_client->expects($this->once())->method('post')->with(
            $this->resource_url,
            json_encode($this->request_sample_data)
        );
        $this->http_client->method('getStatus')->willReturn($response_status);
        $this->http_client->method('getBody')->willReturn(json_encode($this->response_sample_data));
        $client = new Service\Client($this->http_client_factory);
        $r = $client->request(
            'post',
            $this->resource_url,
            $this->request_sample_data
        );
        $this->assertEquals($this->response_sample_data, $r);        
    }

    public function testWithAccessToken() {
        $access_token = 'token';
        $this->http_client->expects($this->once())->method('post')->with(
            $this->resource_url,
            json_encode($this->request_sample_data)
        );
        $this->http_client->method('getStatus')->willReturn(200);
        $this->http_client->expects($this->once())->method('addHeader')->with(
            "Authorization", "Bearer {$access_token}"
        );
        $client = new Service\Client($this->http_client_factory);
        $client->setAccessToken($access_token);
        $client->request(
            'post',
            $this->resource_url,
            $this->request_sample_data
        );
    }

    public function testSendRequest200() {
        $this->checkValidRequest(200);
    }

    public function testSendRequest201() {
        $this->checkValidRequest(201);
    }    
    
    public function testResponseStatusNotValid() {
        $this->http_client->method('getStatus')->willReturn(400);
        $client = new Service\Client($this->http_client_factory);
        $this->expectException(Exception\ServiceException::class);
        try {
            $result = $client->request('post', $this->resource_url, $this->request_sample_data);
        } catch (Exception\ServiceException $ex) {
            $this->assertEquals($ex->getErrorCode(), 'status_code_not_valid');
            $data = $ex->getData();
            $this->assertEquals($data['status_code'], 400);
            throw $ex;
        }
    }
    
}