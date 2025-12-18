<?php
namespace WeltPixel\SmartProductTabs\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddLabelColumns implements SchemaPatchInterface, PatchVersionInterface
{
    /**
     * @var SchemaSetupInterface $schemaSetup
     */
    private $schemaSetup;

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $installer = $this->schemaSetup;
        $this->schemaSetup->startSetup();

        $tableName = $installer->getTable('weltpixel_smartproducttabs');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $installer->getConnection()
                ->addColumn(
                    $tableName,
                    'display_label',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => true,
                        'unsigned' => true,
                        'comment'  => 'Display Label',
                        'default'  => 0,
                        'after'    => 'title'
                    ]
                );

            $installer->getConnection()
                ->addColumn(
                    $tableName,
                    'label_text',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment'  => 'Label Text',
                        'default'  => '',
                        'after'    => 'display_label'
                    ]
                );

            $installer->getConnection()
                ->addColumn(
                    $tableName,
                    'label_attribute',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment'  => 'Label Attribute',
                        'default'  => '',
                        'after'    => 'label_text'
                    ]
                );
        }

        $this->schemaSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.4';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            ChangeTableStructure::class
        ];
    }
}
