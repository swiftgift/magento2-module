<?php
namespace Swiftgift\Gift\Block;

class ElementBase extends \Magento\Framework\View\Element\Template {
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swiftgift\Gift\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    protected function prepareBlockData()
    {
        $this->addData([
            'show'=>$this->helper->isCanUse()
        ]);
    }    
    
}
