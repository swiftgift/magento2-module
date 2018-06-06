<?php
namespace Swiftgift\Gift\Controller\Checkout;
use \Magento\Framework\App\Action\Action;

class Enable extends Action {

    protected $quoteLoaderFactory;
    protected $sg_quote;
    protected $payment_method_management;
    protected $cartTotalRepository;
    protected $quoteRepository;
    


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swiftgift\Gift\Checkout\QuoteLoaderFactory $quoteLoaderFactory,
        \Swiftgift\Gift\Checkout\Quote $sg_quote,
        \Magento\Quote\Api\PaymentMethodManagementInterface $payment_method_management,
        \Magento\Quote\Model\Cart\CartTotalRepository $cartTotalRepository
    ) {
        parent::__construct($context);
        $this->quoteLoaderFactory = $quoteLoaderFactory;
        $this->sg_quote = $sg_quote;
        $this->payment_method_management = $payment_method_management;
        $this->cartTotalRepository = $cartTotalRepository;
    }

    protected function getQuote($cart_id) {
        return $this->quoteLoaderFactory->create()->loadByCartId($cart_id);
    }

    public function execute() {
        $request = $this->getRequest();
        $cart_id = $request->getParam('cart_id');
        $result = ['success'=>TRUE, 'newveer'=>1];
        $quote = $this->getQuote($cart_id);
        $result = ['success'=>FALSE];
        if ($quote) {
            $data = $request->getPost();
            $_result = $this->sg_quote->setSwiftGiftUsed(
                $quote,
                $request->getPost()
            );
            if ($_result) {
                try {
                    $quote->getShippingAddress()
                        ->setCountryId($data['country_code'])
                        ->setRegion($data['region'])
                        ->setRegionId($data['region_id'])
                        ->setShippingMethod("{$data['carrier_code']}_{$data['shipping_method_code']}")
                        ->setBaseSubtotal($quote->getBaseSubtotal())
                        ->setCollectShippingRates(TRUE)->collectShippingRates()
                        ->save();
                    $quote->setCollectedFlag(FALSE)->collectTotals()->save();
                } catch (\Magento\Framework\Validator\Exception $ex) {
                    $_result = [$ex->getMessage()];
                }
            }
            if ($_result === true) {
                $result['success'] = true;
                $result['payment_methods'] = array_map(function($m) {
                    return [
                        'code'=>$m->getCode(),
                        'title'=>$m->getTitle()
                    ];
                }, $this->payment_method_management->getList($quote->getId()));
                $result['totals'] = $this->getTotalsForQuote($quote);
            } else {
                $result['errors'] = $_result;
            }
        } else {
            $result['errors'] = ['cant load quote'];
        }
        $this->getResponse()->setBody(json_encode($result));
        
    }

    protected function getTotalsForQuote($quote) {
        $totals = $this->cartTotalRepository->get($quote->getId())->toArray();
        $totals['total_segments'] = array_map(function($total_segment) {
            return $total_segment->toArray();
        }, $totals['total_segments']);
        $totals['items'] = array_map(function($item) {
            return $item->__toArray();
        }, $totals['items']);
        return $totals;
    }
    
}