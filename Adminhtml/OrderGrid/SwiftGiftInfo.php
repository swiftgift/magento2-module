<?php
namespace Swiftgift\Gift\Adminhtml\OrderGrid;

class SwiftGiftInfo extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $giftCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Swiftgift\Gift\Model\ResourceModel\Gift\CollectionFactory $giftCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->giftCollectionFactory = $giftCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        $gifts = [];
        foreach ($this->giftCollectionFactory->create()->getData() as $gift_data) {
            $gifts[$gift_data['order_id']] = $gift_data;
        }
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $value = '---';
                if (isset($gifts[(int)$item['entity_id']])) {
                    $gift = $gifts[(int)$item['entity_id']];
                    $value = "Gift status: {$gift['status']}; Code: {$gift['code']}";
                }
                $item[$this->getData('name')] = $value;
            }
        }
        return $dataSource;
    }
    
}