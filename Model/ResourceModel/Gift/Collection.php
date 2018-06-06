<?php
namespace Swiftgift\Gift\Model\ResourceModel\Gift;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'swift_gift_collection';
	protected $_eventObject = 'swift_gift_collection';

    protected function _construct() {
        $this->_init('Swiftgift\Gift\Model\Gift', 'Swiftgift\Gift\Model\ResourceModel\Gift');
    }
    
}