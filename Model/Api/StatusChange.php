<?php
namespace Swiftgift\Gift\Model\Api;

class StatusChange implements \Swiftgift\Gift\Api\StatusChangeInterface {

    protected $statusChangeHandlerFactory;
    protected $extResultInterfaceFactory;

    public function __construct(        
        \Swiftgift\Gift\Service\GiftStatusChangeHandlerFactory $statusChangeHandlerFactory,
        \Swiftgift\Gift\Api\Data\ExtResultInterfaceFactory $extResultInterfaceFactory
    ) {
        $this->statusChangeHandlerFactory = $statusChangeHandlerFactory;
        $this->extResultInterfaceFactory = $extResultInterfaceFactory;
    }

    public function statusChange($protect_code, $gift_id, $status, $delivery_address=null) {
        $result = $this->extResultInterfaceFactory->create();
        $result->setSuccess(TRUE);
        try {
            $this->statusChangeHandlerFactory->create()->handle(
                $gift_id,
                $protect_code,
                $status
            );
        } catch (\Swiftgift\Gift\Exception\ServiceException $ex)  {
            $result->setSuccess(FALSE);
        }
        return $result;
    }
    
}