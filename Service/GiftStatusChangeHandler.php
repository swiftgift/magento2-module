<?php
namespace Swiftgift\Gift\Service;
use \Swiftgift\Gift\Exception;

class GiftStatusChangeHandler {

    const STATUS_COMPLETE = 'accepted';

    protected $gift_factory;
    protected $orderFactory;
    protected $protect_code_factory;
    protected $logger;    

    public function __construct(
        \Swiftgift\Gift\Model\GiftFactory $gift_factory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Swiftgift\Gift\Service\ProtectCodeFactory $protect_code_factory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->gift_factory = $gift_factory;
        $this->orderFactory = $orderFactory;
        $this->protect_code_factory = $protect_code_factory;
        $this->logger = $logger;
    }

    public function handle($gift_id, $protect_code, $status, $delivery_address=NULL) {
        $delivery_addr_str = json_encode($delivery_address);
        $this->logger->addInfo("Swiftgift:status_change: start {$gift_id}, {$status}, {$delivery_addr_str}");
        if ($this->protect_code_factory->create()->isValid($protect_code, $gift_id)) {
            $this->logger->addInfo('Swiftgift:status_change Protect code valid');
            $gift = $this->gift_factory->create()->load($gift_id);
            if (!$gift->getId()) {
                throw new Exception\ServiceException("gift_not_exists", [], "gift with id={$gift_id} not exists.");
            }
            $this->logger->addInfo('Swiftgift:status_change Gift exists');
            if ($status === self::STATUS_COMPLETE) {
                $this->logger->addInfo('Swiftgift:status_change status is complete.');
                if (!$delivery_address) {
                    throw new Exception\ServiceException('delivery_address_empty', [], 'delivery_address must not be empty');
                }
                $order = $this->orderFactory->create()->load($gift->getOrderId());
                if (!$order->getId()) {
                    throw new Exception\ServiceException("order_not_exists", [], "Cant load order with id \"{$gift->getOrderId()}\"");
                }
                $order->getShippingAddress(TRUE)->addData($delivery_address)->save();
                $this->logger->addInfo('Swiftgift:status_change save address with phone: {$order->getShippingAddress()->getTelephone()}');
            } else {
                $this->logger->addInfo('Swiftgift:status_change status is not complete.');
            }
            $gift->setNewStatus($status)->save();
            $this->logger->addInfo('Swiftgift:status_change save gift status');
            return $gift;
        } else {
            throw new Exception\ServiceException('code_not_valid');
        }
    }
    
}
