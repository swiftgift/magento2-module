<?php
namespace Swiftgift\Gift\Block\Adminhtml\Order\View\Tab;
class Info extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    protected $giftModelFactory;
    protected $gift;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = [],
        \Swiftgift\Gift\Model\GiftFactory $giftModelFactory
    ) {
        parent::__construct(
            $context,
            $registry,
            $adminHelper,
            $data
        );
        $this->giftModelFactory = $giftModelFactory;
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getSource()
    {
        return $this->getOrder();
    }

    public function getGiftByOrderId($order_id) {
        $gift = null;
        $gift_current = $this->giftModelFactory->create()->load($order_id, 'order_id');
        if ($gift_current->getId()) {
            $gift = $gift_current;
        }
        return $gift;
    }

    public function getTabLabel()
    {
        return 'SwiftGift';
    }

    public function getTabTitle()
    {
        return 'SwiftGift order info';
    }

    protected function _beforeToHtml()
    {
        $order = $this->getOrder();
        $this->addData([
            'gift'=>$this->getGiftByOrderId($order->getId())
        ]);
        return parent::_beforeToHtml();
    }

    public function canShowTab()
    {
        $can_show = false;
        $order = $this->getOrder();
        if ($order && $order->getId()) {
            $can_show = $this->getGiftByOrderId($order->getId()) != null;
        }
        return $can_show;
    }

    public function isHidden()
    {
        return false;
    }

}
