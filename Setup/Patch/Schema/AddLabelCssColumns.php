<?php
namespace WeltPixel\SmartProductTabs\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddLabelCssColumns implements SchemaPatchInterface, PatchVersionInterface
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
                    'label_bg_border_radius',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 32,
                        'nullable' => true,
                        'comment'  => 'Label Background Border Radius',
                        'default'  => '',
                        'after'    => 'label_text'
                    ]
                );
            $installer->getConnection()
                ->addColumn(
                    $tableName,
                    'label_bg_color',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 32,
                        'nullable' => true,
                        'comment'  => 'Label Background Color',
                        'default'  => '',
                        'after'    => 'label_text'
                    ]
                );
            $installer->getConnection()
                ->addColumn(
                    $tableName,
                    'label_font_color',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 32,
                        'nullable' => true,
                        'comment'  => 'Label Font Color',
                        'default'  => '',
                        'after'    => 'label_text'
                    ]
                );
            $installer->getConnection()
                ->addColumn(
                    $tableName,
                    'label_font_size',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 32,
                        'nullable' => true,
                        'comment'  => 'Label Font Size',
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
        return '1.0.5';
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
            AddLabelColumns::class
        ];
    }
}
