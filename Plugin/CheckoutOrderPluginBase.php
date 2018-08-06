<?php
namespace Swiftgift\Gift\Plugin;
use Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Framework\App\ResourceConnection;

class CheckoutOrderPluginBase {

    protected $sg_checkout_order_factory;
    protected $order_repository;
    protected $quote_repository;
    protected $quote_id_mask_factory;
    protected $logger;
    protected $helper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Swiftgift\Gift\Checkout\OrderFactory $sg_checkout_order_factory,
        \Magento\Sales\Model\OrderRepository $order_repository,
        \Magento\Quote\Model\QuoteRepository $quote_repository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quote_id_mask_factory,
        ResourceConnection $resource,
        \Swiftgift\Gift\Helper\Data $helper
    ) {
        $this->logger = $logger;
        $this->sg_checkout_order_factory = $sg_checkout_order_factory;
        $this->order_repository = $order_repository;
        $this->quote_repository = $quote_repository;
        $this->quote_id_mask_factory = $quote_id_mask_factory;
        $this->resource = $resource;
        $this->helper = $helper;
    }

    protected function handlePlaceOrder($quote, $methodClosure) {
        if ($this->helper->isCanUse() && $quote->getSwiftGiftUsed() === '1') {
            $gift_data = [
                'name'=>$quote->getSwiftGiftName(),
                'message'=>$quote->getSwiftGiftMessage(),
                'country_code'=>$quote->getSwiftGiftCountryCode()
            ];
            $conn = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
            $conn->beginTransaction();
            try {
                $orderId = $methodClosure();
                $order = $this->order_repository->get($orderId);
                $gift = $this->sg_checkout_order_factory->create()->createGiftAfterPlaceOrder(
                    $order,
                    $gift_data
                );
                $gift->save();
                $order->setSwiftGiftUsed(TRUE);
                $order->getShippingAddress(TRUE)
                    ->setCountryId($quote->getSwiftGiftCountryCode())
                    ->setRegion($quote->getSwiftGiftRegion())
                    ->setRegionId($quote->getSwiftGiftRegionId());
                $this->order_repository->save($order);
                $conn->commit();
                return $orderId;
            } catch (\Exception $ex) {
                $conn->rollBack();
                $this->logger->error("Swiftgift: Error on create gift: {$ex->getMessage()}");
                throw new CouldNotSaveException(__('Something went wrong during Magic Link creation.'));
            }
        } else {
            return $methodClosure();
        }
    }
    
}
