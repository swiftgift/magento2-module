<?php
namespace Swiftgift\Gift\Service;

class Service {

    protected $client_factory;
    protected $access_token_provider_factory;
    protected $gift_url;

    public function __construct(
        \Swiftgift\Gift\Service\ClientFactory $client_factory,
        \Swiftgift\Gift\Service\AccessTokenProviderFactory $access_token_provider_factory,
        $gift_url
    ) {
        $this->client_factory = $client_factory;
        $this->access_token_provider_factory = $access_token_provider_factory;
        $this->gift_url = $gift_url;
    }

    public function request($method, $request_url, array $data=array()) {
        $access_token_provider = $this->access_token_provider_factory->create();
        if (!$access_token_provider->getAccessToken()) {
            $access_token_provider->obtainNewAccessToken();
        }
        $client = $this->client_factory->create();
        $client->setAccessToken($access_token_provider->getAccessToken());
        $need_try_with_new_token = false;
        $ex = null;
        $result = null;
        try {
            $result = $client->request($method, $request_url, $data);
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            $ex = $ex;
            $ex_data = $ex->getData();
            if ($ex_data['error_code'] === 'auth_required') {
                $need_try_with_new_token = true;
            }
        }
        if ($ex) {
            if ($need_try_with_new_token) {
                $access_token_provider->obtainNewAccessToken();
                $client->setAccessToken(
                    $access_token_provider->getAccessToken()
                );
                $result = $client->request($method, $request_url, $data);
            } else {
                throw $ex;
            }
        }
        return $result;
    }

    public function createGift($data) {
        return $this->request(
            'POST',
            $this->gift_url,
            $data
        );
    }
    
}