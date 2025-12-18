<?php
namespace WeltPixel\SmartProductTabs\Helper;

use Magento\Catalog\Model\Product;

/**
 * Class Data
 * @package WeltPixel\SmartProductTabs\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    protected $_scopeValue = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_labelPrefixTag = "<span class='tab-title-label'>";
    protected $_labelPostfixTag = "</span>";

    /**
     * @var string
     */
    protected $_tabName = [
        'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_1',
        'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_2',
        'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_3'
    ];

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
    }

    /**
     * @return string
     */
    public function getTabNameA()
    {
        $tabName = $this->scopeConfig->getValue($this->_tabName[0], $this->_scopeValue);
        $tabLabelValue = $this->getTabLabel(1);

        if ($tabLabelValue) {
            $tabLabelValue = $this->_labelPrefixTag . $tabLabelValue . $this->_labelPostfixTag;
        }

        if (empty($tabName)) {
            return 'Smart Product Tab' . $tabLabelValue;
        }
        return $tabName . $tabLabelValue;
    }

    /**
     * @return string
     */
    public function getTabNameB()
    {
        $tabName = $this->scopeConfig->getValue($this->_tabName[1], $this->_scopeValue);
        $tabLabelValue = $this->getTabLabel(2);

        if ($tabLabelValue) {
            $tabLabelValue = $this->_labelPrefixTag . $tabLabelValue . $this->_labelPostfixTag;
        }

        if (empty($tabName)) {
            return 'Smart Product Tab' . $tabLabelValue;
        }
        return $tabName . $tabLabelValue;
    }

    /**
     * @return string
     */
    public function getTabNameC()
    {
        $tabName = $this->scopeConfig->getValue($this->_tabName[2], $this->_scopeValue);
        $tabLabelValue = $this->getTabLabel(2);

        if ($tabLabelValue) {
            $tabLabelValue = $this->_labelPrefixTag . $tabLabelValue . $this->_labelPostfixTag;
        }

        if (empty($tabName)) {
            return 'Smart Product Tab' . $tabLabelValue;
        }
        return $tabName . $tabLabelValue;
    }

    /**
     * @return bool
     */
    public function isSmartProductTabsEnabled()
    {
        return (bool)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/enable_smartproducttabs', $this->_scopeValue);
    }

    /**
     * @param string $systemTabName
     * @return bool
     */
    public function isChangesEnbledForTab($systemTabName)
    {
        return (bool)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_changes_enable', $this->_scopeValue);
    }

    /**
     * @param string $systemTabName
     * @return bool
     */
    public function canShowTab($systemTabName)
    {
        return (bool)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_show', $this->_scopeValue);
    }

    /**
     * @param string $systemTabName
     * @return int
     */
    public function getPositionForTab($systemTabName)
    {
        return (int)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_position', $this->_scopeValue) ?? 0;
    }

    /**
     * @param $systemTabName
     * @return string
     */
    public function getTitleTabLabel($systemTabName)
    {
        $labelValue = '';
        $displayLabel = $this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_display_label', $this->_scopeValue) ?? false;

        if (!$displayLabel) {
            return $labelValue;
        }

        $displayLabelText = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_text', $this->_scopeValue) ?? '');

        if ($displayLabelText) {
            return $labelValue . $this->_labelPrefixTag . $displayLabelText . $this->_labelPostfixTag;
        }

        $labelAttribute =  $this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_attribute', $this->_scopeValue) ?? '';
        $productLabelAttributeValue = '';

        if ($labelAttribute && $this->getProduct()) {
            $product = $this->getProduct();
            $productLabelAttributeValue = $this->getProductLabelAttributeValue($product, $labelAttribute);

            if ($productLabelAttributeValue) {
                return $labelValue . $this->_labelPrefixTag . $productLabelAttributeValue . $this->_labelPostfixTag;
            }
        }

        return $labelValue;
    }

    /**
     * @param string $systemTabName
     * @return string
     */
    public function getTitleForTab($systemTabName)
    {
        return $this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title', $this->_scopeValue) ?? '';
    }

    /**
     * @return string
     */
    public function getSystemTabsInlineCss()
    {
       $tabNames = [
            'reviews' => 'tab-label-reviews',
            'moreinformation' => 'tab-label-additional',
            'details' => 'tab-label-description'
       ];

       $tabInlinesCss = '';

       foreach ($tabNames as $tabName => $tabId) {
           $tabNameCss = $this->getInlineCssForTabLabel($tabName);
           if ($tabNameCss) {
               $tabInlinesCss .= "#" . $tabId . ' span.tab-title-label{' . $tabNameCss . '}';
           }
       }

       return $tabInlinesCss;
    }

    /**
     * @param $systemTabName
     * @return string
     */
    protected function getInlineCssForTabLabel($systemTabName) {
        $tabInlinesCss = '';
        $displayLabel = $this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_display_label', $this->_scopeValue) ?? false;

        if (!$displayLabel) {
            return $tabInlinesCss;
        }

        $labelFontSize = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_font_size', $this->_scopeValue) ?? '');
        $labelFontColor = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_font_color', $this->_scopeValue) ?? '');
        $labelBgColor = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_bg_color', $this->_scopeValue) ?? '');
        $labelBorderRadius = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_bg_border_radius', $this->_scopeValue) ?? '');
        $labelPadding = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title_label_padding', $this->_scopeValue) ?? '');

        if ($labelFontSize) {
            $tabInlinesCss .= 'font-size: ' . $labelFontSize . ';';
        }
        if ($labelFontColor) {
            $tabInlinesCss .= 'color: ' . $labelFontColor . ';';
        }
        if ($labelBgColor) {
            $tabInlinesCss .= 'background-color: ' . $labelBgColor . ';';
        }
        if ($labelBorderRadius) {
            $tabInlinesCss .= 'border-radius: ' . $labelBorderRadius . ';';
            $tabInlinesCss .= '-moz-border-radius: ' . $labelBorderRadius . ';';
            $tabInlinesCss .= '-webkit-border-radius: ' . $labelBorderRadius . ';';
        }
        if ($labelPadding) {
            $tabInlinesCss .= 'padding: ' . $labelPadding . ';';
        }
        return $tabInlinesCss;
    }

    /**
     * @param string $systemTabName
     * @return array
     */
    public function getSystemTabOptions($systemTabName)
    {
        $tabName = '';
        switch ($systemTabName) {
            case 'reviews.tab':
                $tabName = 'reviews';
                break;
            case 'product.attributes':
                $tabName = 'moreinformation';
                break;
            case 'product.info.description':
                $tabName = 'details';
                break;
        }
        $tabPosition =  trim($this->getPositionForTab($tabName));
        $tabTitle = trim($this->getTitleForTab($tabName));
        $changeSortOrder = false;
        $changeTitle = false;
        $tabTitleLabel = $this->getTitleTabLabel($tabName);
        if (strlen($tabPosition)) {
            $changeSortOrder = true;
        }
        if (strlen($tabTitle)) {
            $changeTitle = true;
        }
        return [
            'changes_enabled' => (bool) ($tabName) ? $this->isChangesEnbledForTab($tabName) : false,
            'show' =>  (bool) ($tabName) ? $this->canShowTab($tabName) : true,
            'change_sort_order' => (bool) $changeSortOrder,
            'sort_order' => $tabPosition,
            'change_title' => (bool) $changeTitle,
            'title' => $tabTitle,
            'tab_label' => $tabTitleLabel
        ];
    }

    /**
     * Returns a Product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * @param $index
     * @return string
     */
    public function getTabLabel($index)
    {
        $displayLabel = $this->scopeConfig->getValue('weltpixel_smartproducttabs/general/attribute_smartproducttabs_display_label_' . $index );
        if (!$displayLabel) {
            return '';
        }

        $displayLabelText = trim($this->scopeConfig->getValue('weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_label_text_' . $index ) ?? '');

        if ($displayLabelText) {
            return $displayLabelText;
        }

        $labelAttribute = $this->scopeConfig->getValue('weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_label_attribute_' . $index );
        $productLabelAttributeValue = '';

        if ($labelAttribute && $this->getProduct()) {
            $product = $this->getProduct();
            $productLabelAttributeValue = $this->getProductLabelAttributeValue($product, $labelAttribute);
        }

        return $productLabelAttributeValue;
    }

    /**
     * @param $product
     * @param $labelAttribute
     * @return mixed|string
     */
    public function getProductLabelAttributeValue($product, $labelAttribute) {
        $productAttributeOptions = [];
        $productLabelAttributeValue = '';

        try {
            $options = $product->getResource()->getAttribute($labelAttribute)->getSource()->getAllOptions();
            foreach ($options as $option) {
                $productAttributeOptions[$option['value']] = $option['label'];
            }
        } catch (Exception $ex) {
        }

        $frontendValue =  $product->getData($labelAttribute);

        if (is_array($frontendValue)) {
            $result = [];
            foreach ($frontendValue as $value) {
                $result[] = ($productAttributeOptions[$value]) ? $productAttributeOptions[$value] : null;
            }
            $productLabelAttributeValue = implode(',', array_filter($result));
        } elseif (isset($productAttributeOptions[$frontendValue])) {
            $productLabelAttributeValue = $productAttributeOptions[$frontendValue];
        } else {
            $productLabelAttributeValue = $frontendValue ?? '';
        }
        return $productLabelAttributeValue;
    }

    /**
     * @return mixed|string
     */
    public function getLabelPrefix() {
        return $this->_labelPrefixTag;
    }

    /**
     * @return mixed|string
     */
    public function getLabelPostfix() {
        return $this->_labelPostfixTag;
    }
}
