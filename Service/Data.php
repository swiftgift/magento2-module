<?php
namespace Swiftgift\Gift\Service;
use Swiftgift\Gift\Utils;
use Swiftgift\Gift\Exception;

class Data {

    protected $sg_validator_factory;

    public function __construct(
        \Swiftgift\Gift\Checkout\ValidatorFactory $sg_validator_factory
    ) {
        $this->sg_validator_factory = $sg_validator_factory;
    }

    public function createGiftDataFromCheckout(\Magento\Sales\Model\Order $order, array $sg_form_data, array $options) {
        $v = $this->sg_validator_factory->create();
        if ($v->isValid($sg_form_data)) {
            $data = array();
            $data = array(
                'idempotency_key'=>$order->getProtectCode(),
                'sender'=>array(
                    'name'=>$sg_form_data['name'],
                    'image_url'=>$options['image_url'],
                    'billing'=>$this->createGiftBillingData($order),
                ),
                'message'=>array(
                    'text'=>$sg_form_data['message'],
                    'image_url'=>$options['image_url']
                ),
                'delivery'=>array(
                    "country"=> $order->getShippingAddress()->getCountryId(),
                    "state"=> $order->getShippingAddress()->getRegionCode(),
                    "name"=> $order->getShippingDescription(),
                    "min_time"=> 1,
                    "max_time"=> 2
                )
            );
            $data = array_merge($data, $this->createGiftBasketData($order, $options));
            return $data;
        } else {
            throw new Exception\ServiceException('sg_form_data_not_valid', array(
                'messages'=>$v->getMessages()
            ), 'Sg form data not valid');
        }
    }

    protected function createGiftBasketData($order, $options) {
        return array(
            'products'=>array_map(function($item) use ($options) {return array('name'=>$item->getName(), 'image_url'=>$item->getProduct()->getMediaConfig()->getMediaUrl($item->getProduct()->getImage()));}, $order->getAllItems()),
            'basket_amount'=>$order->getGrandTotal(),
            'currency'=>$order->getGlobalCurrencyCode(),
        );
    }

    protected function createGiftBillingData($order) {
        return $order->getBillingAddress()->getData();
    }
    
}
