<?php
namespace WeltPixel\SmartProductTabs\Model\SmartProductTabs\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;


/**
 * Class Status
 * @package WeltPixel\SmartProductTabs\Model\SmartProductTabs\Attribute\Source
 */
class ProductAttribute implements OptionSourceInterface
{
    /**
     * @var AttributeFactory
     */
    private $_attributeFactory;

    /**
     * @var TypeFactory
     */
    protected $eavTypeFactory;

    /**
     * @param  $attributeFactory
     * @param TypeFactory $typeFactory
     */
    public function __construct(AttributeFactory $attributeFactory, TypeFactory $typeFactory)
    {
        $this->_attributeFactory = $attributeFactory;
        $this->eavTypeFactory = $typeFactory;
    }

    /**
     * @return array
     */
    public function getAvailableAttributes()
    {
        $arr = [
            false =>  __('-- Please Select Attribute --')
        ];

        $entityType = $this->eavTypeFactory->create()->loadByCode('catalog_product');
        $attributesCollection = $this->_attributeFactory->create()->getCollection();
        $attributesCollection
            ->addFieldToFilter('entity_type_id', $entityType->getId());
        foreach ($attributesCollection as $attribute) {
            if ($attribute->getData('frontend_label')) {
                $arr[$attribute->getData('attribute_code')] = $attribute->getData('frontend_label');
            }
        }
        return $arr;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach ($this->getAvailableAttributes() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value)
    {
        $options = $this->getAvailableAttributes();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
