<?php
namespace Swiftgift\Gift\Model;

class Gift extends \Magento\Framework\Model\AbstractModel {
    const CACHE_TAG = 'swiftgift_gift_gift';

    protected function _construct() {
        $this->_init('Swiftgift\Gift\Model\ResourceModel\Gift');
    }

    public function setNewStatus($status) {
        $this->addData([
            'status'=>$status,
            'status_change_time'=>time()
        ]);
        return $this;
    }
    
}