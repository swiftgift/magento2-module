<?php
namespace Swiftgift\Gift\Checkout;
use \Swiftgift\Gift\Exception;

class Order {

    protected $logger;
    protected $sg_service_factory;
    protected $sg_service_data;
    protected $gift_model_factory;
    protected $protect_code_factory;
    protected $base_url;
    protected $utils;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Swiftgift\Gift\Service\ServiceFactory $sg_service_factory,
        \Swiftgift\Gift\Service\Data $sg_service_data,
        \Swiftgift\Gift\Model\GiftFactory $gift_model_factory,
        \Swiftgift\Gift\Service\ProtectCodeFactory $protect_code_factory,
        \Swiftgift\Gift\Utils $utils,
        $base_url
    ) {
        $this->logger = $logger;
        $this->sg_service_factory = $sg_service_factory;
        $this->sg_service_data = $sg_service_data;
        $this->gift_model_factory = $gift_model_factory;
        $this->protect_code_factory = $protect_code_factory;
        $this->utils = $utils;
        $this->base_url = $base_url;
    }

    public function createGiftAfterPlaceOrder(\Magento\Sales\Model\Order $order, $sg_form_data) {
        $order_id = $order->getId();
        $this->logger->info("Swiftgift: start create gift data. Order: #{$order_id}");
        $gift_data = $this->sg_service_data->createGiftDataFromCheckout(
            $order,
            $sg_form_data,
            array(
                'base_url'=>$this->base_url,
                'image_url'=>NULL
            )
        );
        $gift_data_str = json_encode($gift_data);
        $this->logger->info("Swiftgift: gift data created. ---{$gift_data_str}---");
        $gift = $this->gift_model_factory->create();
        $gift->addData([
            'order_id'=>$order->getId(),
            'status'=>'initialized',
        ]);
        $gift->setStatusChangeTime(time());
        $gift->save();
        $gift_id = $gift->getId();
        $protect_code = $this->protect_code_factory->create()->generate($gift->getId());
        $gift_data['callback_url'] = $this->utils->joinWithTrim([
            $this->base_url,
            "swiftgift/gift/statuschange/gift_id/{$gift_id}/code/{$protect_code}"
        ]);
        $this->logger->info("Swiftgift: Start create gift.");
        $gift_result_data = $this->sg_service_factory->create()->createGift($gift_data);
        $gift_result_data_str = json_encode($gift_result_data);
        $this->logger->info("Swiftgift: Gift created. ---{$gift_result_data_str}---");
        $gift->addData(array(
            'status'=>'pending',
            'code'=>$gift_result_data['code'],
            'share_url'=>$gift_result_data['share_url']
        ));
        $gift->setStatusChangeTime(time());
        $gift->save();
        $this->logger->info("Swiftgift: Gift saved. Id: {$gift->getId()}. Status: {$gift->getStatus()}");
        return $gift;
    }
    
}
