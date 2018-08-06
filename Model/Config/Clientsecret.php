<?php
namespace Swiftgift\Gift\Model\Config;

class Clientsecret extends \Magento\Framework\App\Config\Value {

    protected $accessTokenProviderFactory;

    public function __construct(
        \Swiftgift\Gift\Service\AccessTokenProviderFactory $accessTokenProviderFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection
        );
        $this->accessTokenProviderFactory = $accessTokenProviderFactory;
    }

    public function beforeSave() {
        $r = parent::beforeSave();
        $data = $this->_getData('fieldset_data');
        $base_url = $data['api_base_url'];
        $client_secret = $data['client_secret'];
        $accessTokenProvider = $this->accessTokenProviderFactory->create(
            $base_url,
            $client_secret
        );
        try {
            $access_token = $accessTokenProvider->obtainNewAccessToken();
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex) {
            throw new \Magento\Framework\Exception\ValidatorException(__('Please check you API URL and Secret Key.'));
        }
        return $r;
    }
    
}
