<?php
namespace Swiftgift\Gift\Checkout;

class ValidatorFactory {

    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create() {
        $available_countries_codes = array_map(function($c) {return $c['iso2_code'];}, $this->objectManager->get(\Swiftgift\Gift\AvailableCountries::class)->getAvailableCountries()->getData());
        $prop_type = \Magento\Framework\Validator\Config::CONSTRAINT_TYPE_PROPERTY;
        $not_empty = array(
            'type'=>$prop_type,
            'class'=>'\Magento\Framework\Validator\NotEmpty',
            'options'=>array(
                'arguments'=>array(
                    'options'=>array('type'=>'all')
                )
            )
        );
        $country_code_rule = array(
            'type'=>$prop_type,
            'class'=>'\Swiftgift\Gift\Checkout\Validator\CountryCode',
            'options'=>array(
                'arguments'=>array(
                    'available_countries_codes'=>$available_countries_codes
                )
            )
        );
        $builder = $this->objectManager->create(\Magento\Framework\Validator\Builder::class, [
            'constraints'=>[
                array_merge(array(
                    'property'=>'country_code',
                    'alias'=>'country_code'
                ), $not_empty),
                array_merge(array(
                    'property'=>'country_code',
                    'alias'=>'country_code'
                ), $country_code_rule)
            ]
        ]);
        return $builder->createValidator();
    }
    
}
