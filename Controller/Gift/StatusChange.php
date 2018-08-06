<?php
namespace Swiftgift\Gift\Controller\Gift;
use \Magento\Framework\App\Action\Action;
use \Swiftgift\Gift\Exception;

class StatusChange extends Action {

    protected $giftStatusChangeHandlerFactory;
    protected $utils;
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swiftgift\Gift\Service\GiftStatusChangeHandlerFactory $giftStatusChangeHandlerFactory,
        \Swiftgift\Gift\Utils $utils,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->giftStatusChangeHandlerFactory = $giftStatusChangeHandlerFactory;
        $this->utils = $utils;
        $this->logger = $logger;
    }

    public function execute() {
        $gift_id = $this->getRequest()->getParam('gift_id');
        $code = $this->getRequest()->getParam('code');
        $data = json_decode($this->getRequest()->getContent(), TRUE);
        $this->logger->info("Swiftgift: status change: " . json_encode(
            [
                'gift_id'=>$gift_id,
                'code'=>$code,
                'data'=>$data
            ]
        ));
        $gift_status_change_handler = $this->giftStatusChangeHandlerFactory->create();        
        $result = [
            'status'=>200,
            'data'=>[
                'success'=>TRUE,
                'errors'=>[]
            ]
        ];
        if ($gift_id && $code && $data && isset($data['status'])) {
            try {
                $gift_status_change_handler->handle(
                    $gift_id,
                    $code,
                    $data['status'],
                    $data['status'] === 'accepted' ? $this->utils->prepareDeliveryAddressData($data['delivery_address']) : null
                );
            } catch (Exception\ServiceException $ex) {
                $status = 500;
                if ($ex->getErrorCode() === 'gift_not_exists') {
                    $status = 404;
                } elseif ($ex->getErrorCode() === 'code_not_valid') {
                    $status = 403;
                }
                $result['status'] = $status;
                $result['data']['success'] = FALSE;
                $result['data']['errors'] = [['code'=>$ex->getErrorCode()]];
            }
        } else {
            $result['status'] = 400;
            $result['data']['success'] = FALSE;
        }
        $this->getResponse()->setStatusCode($result['status'])->setBody(json_encode($result['data']));
    }
    
}
