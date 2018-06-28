<?php
namespace Swiftgift\Gift\Service;
use Swiftgift\Gift\Utils;
use Swiftgift\Gift\Exception;

class Client {

    protected $http_client_factory;
    protected $logger;
    protected $access_token;
    
    public function __construct(
        \Magento\Framework\HTTP\ClientFactory $http_client_factory,
        \Psr\Log\LoggerInterface $logger,
        $access_token=null
    ) {
        $this->logger = $logger;
        $this->http_client_factory = $http_client_factory;
        $this->access_token = $access_token;
    }

    public function request($method, $request_url, array $data=array()) {
        $request_body = $this->prepareRequestBody($data);
        $access_token = $this->getAccessToken();
        $this->logger->info("Send request. {$method}, {$request_url}, {$request_body}. Accesstoken: {$access_token}");
        $client = $this->http_client_factory->create();
        if ($access_token !== null) {
            $client->addHeader("Authorization", "Bearer {$access_token}");
        }
        $client->{$method}($request_url, $request_body);
        return $this->readResponse($client);
    }

    public function setAccessToken($access_token) {
        $this->access_token = $access_token;
    }

    public function getAccessToken() {
        return $this->access_token;
    }

    protected function prepareRequestBody($data) {
        return json_encode($data);
    }

    protected function readResponse($client) {
        $response_data = json_decode($client->getBody(), TRUE);
        $this->logger->info("Response: status: {$client->getStatus()}. Body: {$client->getBOdy()}");
        if (!in_array($client->getStatus(), array(100, 200, 201), FALSE)) {
            throw new Exception\ServiceException('status_code_not_valid', array(
                'status_code'=>$client->getStatus(),
                'data'=>$response_data
            ), 'Status code not valid.');
        }
        if (isset($response_data['error'])) {
            $error_code = isset($response_data['error']['code']) ? $response_data['error']['code'] : 'unknown';
            throw new Exception\ServiceException('response_with_error', array(
                'error_code'=>$error_code,
                'data'=>$response_data
            ), 'Response with error');
        }
        return $response_data;
    }
        
}
