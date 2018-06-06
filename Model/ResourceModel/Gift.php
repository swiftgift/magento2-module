<?php
namespace Swiftgift\Gift\Model\ResourceModel;

class Gift extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('swiftgift_gift', 'id');
    }
    
}