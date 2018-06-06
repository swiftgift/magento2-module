<?php
namespace Swiftgift\Gift;

class AvailableCountries {

    protected $country_collection_factory;

    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $country_collection_factory
    ) {
        $this->country_collection_factory = $country_collection_factory;
    }

    public function getAvailableCountries() {
        return $this->country_collection_factory->create()->loadByStore();
    }
    
}