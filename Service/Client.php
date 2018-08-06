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
        $this->logger->info("Swiftgift: Send request. {$method}, {$request_url}, {$request_body}. Accesstoken: {$access_token}");
        $client = $this->http_client_factory->create();
        if ($access_token !== null) {
            $client->setHeaders('Authorization', "Bearer {$access_token}");
        }
        $client->setUri($request_url)->setMethod('POST')->setRawData($request_body);
        try {
            $r = $client->request();            
        } catch (\Exception $ex) {
            $this->logger->info('Swiftgift: Get exception.');
            $this->logger->error("Swiftgift: Send request error. Exception: {$ex->getMessage()}");
            throw new Exception\ServiceException('cant_send_request', array(
                'method'=>$method,
                'url'=>$request_url,
                'body'=>$request_body
            ), 'Cant send request.');
        }
        return $this->readResponse($r);
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

    protected function readResponse($r) {
        $response_data = json_decode($r->getBody(), TRUE);
        $this->logger->info("Swiftgift: Response: status: {$r->getStatus()}. Body: {$r->getBody()}");
        if (!in_array($r->getStatus(), array(100, 200, 201), FALSE)) {
            $this->logger->error("Swiftgift: Response status not valid. Status: {$r->getStatus()}. Body: {$r->getBody()}.");
            throw new Exception\ServiceException('status_code_not_valid', array(
                'status_code'=>$r->getStatus(),
                'data'=>$response_data
            ), 'Status code not valid.');
        }
        if (isset($response_data['error'])) {
            $this->logger->error("Swiftgift: Error in b2b api: {$r->getBody()}");
            $error_code = isset($response_data['error']['code']) ? $response_data['error']['code'] : 'unknown';
            throw new Exception\ServiceException('response_with_error', array(
                'error_code'=>$error_code,
                'data'=>$response_data
            ), 'Response with error');
        }
        return $response_data;
    }
        
}
