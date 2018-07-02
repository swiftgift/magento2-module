<?php
namespace Swiftgift\Gift\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
		$installer->startSetup();
        $conn = $setup->getConnection();
        $tableName = $installer->getTable('swiftgift_gift');
        if (!$conn->isTableExists($tableName)) {
            $table = $installer->getConnection()->newTable($tableName)
                   ->addColumn(
                       'id',
                       \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                       null,
                       ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
                   )
                   ->addColumn(
                       'order_id',
                       \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                       null,
                       ['unsigned' => true, 'nullable' => false]
                   )
                   ->addColumn(
                       'status',
                       \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       50,
                       ['nullable' => false],
                       'status'
                   )
                   ->addColumn(
                       'code',
                       \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       100,
                       ['nullable' => true],
                       'code'
                   )
                   ->addColumn(
                       'share_url',
                       \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       100,
                       ['nullable' => true],
                       'share_url'
                   )
                   ->addColumn(
                       'status_change_time',
                       \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                       ['nullable' => false]
                   );
            $installer->getConnection()->createTable($table);
        }
        $quoteTable = $installer->getTable('quote');
        $orderTable = $installer->getTable('sales_order');
        $installer->getConnection()->addColumn(
            $quoteTable,
            'swift_gift_used',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'default'=>'0',
                'nullable'=>FALSE,
                'comment'=>'Swift gift used'
            ]
        );
        $installer->getConnection()->addColumn(
            $quoteTable,
            'swift_gift_name',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'default'=>NULL,
                'comment'=>'Swift gift name'
            ]
        );
        $installer->getConnection()->addColumn(
            $quoteTable,
            'swift_gift_country_code',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'default'=>NULL,
                'comment'=>'Swift gift country code'
            ]
        );
        $installer->getConnection()->addColumn(
            $quoteTable,
            'swift_gift_region_id',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'default'=>NULL,
                'comment'=>'Swift gift region id'
            ]
        );
        $installer->getConnection()->addColumn(
            $quoteTable,
            'swift_gift_region',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'default'=>NULL,
                'comment'=>'Swift gift region'
            ]
        );
        $installer->getConnection()->addColumn(
            $quoteTable,
            'swift_gift_message',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'default'=>NULL,
                'comment'=>'Swift gift message'
            ]
        );
        $installer->getConnection()->addColumn(
            $orderTable,
            'swift_gift_used',
            [
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable'=>FALSE,
                'default'=>'0',
                'comment'=>'Swift gift used'
            ]
        );
        $installer->endSetup();
    }
}
