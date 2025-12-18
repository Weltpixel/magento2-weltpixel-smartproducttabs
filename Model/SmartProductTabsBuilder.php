<?php

namespace WeltPixel\SmartProductTabs\Model;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Store\Model\StoreManagerInterface;
use WeltPixel\SmartProductTabs\Helper\Data as SmartProductTabsHelper;
use WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs\CollectionFactory as SmartProductTabsCollectionFactory;
use Magento\Cms\Model\Template\FilterProvider as CmsFilterProvider;

/**
 * Class SmartProductTabsBuilder
 * @package WeltPixel\SmartProductTabs\Model
 */
class SmartProductTabsBuilder
{
    /**
     * @var string
     */
    protected $indexTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var SmartProductTabsCollectionFactory
     */
    protected $smartProductTabsCollectionFactory;

    /**
     * @var SmartProductTabsFactory
     */
    protected $smartProductTabsFactory;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SmartProductTabsHelper
     */
    protected $smartProductTabsHelper;

    /**
     * @var CmsFilterProvider
     */
    protected $cmsFilterProvider;

    /**
     * @param ResourceConnection $resource
     * @param SmartProductTabsCollectionFactory $smartProductTabsCollectionFactory
     * @param SmartProductTabsFactory $smartProductTabsFactory
     * @param HttpContext $httpContext
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param SmartProductTabsHelper $smartProductTabsHelper
     * @param CmsFilterProvider $cmsFilterProvider
     */
    public function __construct(
        ResourceConnection $resource,
        SmartProductTabsCollectionFactory $smartProductTabsCollectionFactory,
        SmartProductTabsFactory $smartProductTabsFactory,
        HttpContext $httpContext,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        SmartProductTabsHelper $smartProductTabsHelper,
        CmsFilterProvider $cmsFilterProvider
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->smartProductTabsCollectionFactory = $smartProductTabsCollectionFactory;
        $this->smartProductTabsFactory = $smartProductTabsFactory;
        $this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->smartProductTabsHelper = $smartProductTabsHelper;
        $this->cmsFilterProvider = $cmsFilterProvider;
        $this->indexTableName = 'weltpixel_smartproducttabs_rule_idx';
    }

    /**
     * @return string
     */
    public function getIndexTableName()
    {
        return $this->indexTableName;
    }

    /**
     * @return mixed|null
     */
    public function getCustomerGroupId()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreViewId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param int $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSmartProductTabsForProduct($productId)
    {
        /**
         * @TODO ADD enable admin option
         */
        $isSmartProductTabsEnabled = $this->smartProductTabsHelper->isSmartProductTabsEnabled();
        if (!$isSmartProductTabsEnabled) {
            return [];
        }

        return $this->_getTabsForProduct($productId);
    }

    /**
     * @param $productId
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getTabsForProduct($productId)
    {
        $indexTableName = $this->getIndexTableName();
        $indexTable = $this->resource->getTableName($indexTableName);

        $storeId = $this->getStoreViewId();
        $customerGroupId = $this->getCustomerGroupId();

        $smartProductTabsCollection = $this->smartProductTabsCollectionFactory->create();
        $smartProductTabsCollection->addFieldToFilter('main_table.status', 1);
        $smartProductTabsCollection->addFieldToFilter(
            ['main_table.store_id', 'main_table.store_id'],
            [
                ['finset' => [$storeId]],
                ['finset' => [0]]
            ]
        );
        $smartProductTabsCollection->addFieldToFilter('main_table.customer_group', ['finset' => [$customerGroupId]]);
        $smartProductTabsCollection->addFieldToFilter('idx.product_id', $productId);
        $smartProductTabsCollection->addFieldToFilter('idx.store_id', $storeId);
        $smartProductTabsCollection->getSelect()
            ->joinLeft(
                ['idx' => $indexTable],
                "main_table.id = idx.rule_id",
                []
            )
            ->order([
                'main_table.position ASC'
            ]);

        $smartProductTabs = [];
        foreach ($smartProductTabsCollection as $smartProductTab) {
            $smartProductTabs[] = $this->_getSmartTabDetails($smartProductTab->getData());
        }
        return $smartProductTabs;
    }

    /**
     * @param array $tabDetails
     * @return array
     */
    protected function _getSmartTabDetails($tabDetails)
    {
        $tabTitle = $tabDetails['title'];

        if ($tabDetails['display_label']) {
            $displayLabelText = trim($tabDetails['label_text'] ?? '');
            if ($displayLabelText) {
                $tabTitle = $tabTitle . $this->smartProductTabsHelper->getLabelPrefix() . $displayLabelText . $this->smartProductTabsHelper->getLabelPostfix();
            } else {
                $labelAttribute = $tabDetails['label_attribute'];
                $product = $this->smartProductTabsHelper->getProduct();
                if ($labelAttribute && $product) {
                    $productLabelAttributeValue = $this->smartProductTabsHelper->getProductLabelAttributeValue($product, $labelAttribute);
                    if ($productLabelAttributeValue) {
                        $tabTitle = $tabTitle . $this->smartProductTabsHelper->getLabelPrefix() . $productLabelAttributeValue . $this->smartProductTabsHelper->getLabelPostfix();
                    }
                }
            }
        }

        $tabId = '#tab-label-tab-' . preg_replace('/\s*/', '', strtolower(strip_tags($tabTitle))) . '-' . $tabDetails['id'];
        $tabInlinesCss = $this->getInlineCssForTabLabel($tabDetails);

        if (strlen($tabInlinesCss)) {
            $tabInlinesCss = '<style scoped>' . $tabId . ' span.tab-title-label{' . $tabInlinesCss . '}</style>';
        }

        return [
            'id' => $tabDetails['id'],
            'position' => $tabDetails['position'],
            'title' => $tabTitle,
            'content' => $tabInlinesCss . $this->cmsFilterProvider->getBlockFilter()
                ->setStoreId($this->getStoreViewId())
                ->filter($tabDetails['content'])
        ];
    }

    /**
     * @param $tabDetails
     * @return string
     */
    protected function getInlineCssForTabLabel($tabDetails) {
        $tabInlinesCss = '';
        if (isset($tabDetails['label_font_size']) && $tabDetails['label_font_size']) {
            $tabInlinesCss .= 'font-size: ' . $tabDetails['label_font_size'] . ';';
        }
        if (isset($tabDetails['label_font_color']) && $tabDetails['label_font_color']) {
            $tabInlinesCss .= 'color: ' . $tabDetails['label_font_color'] . ';';
        }
        if (isset($tabDetails['label_bg_color']) && $tabDetails['label_bg_color']) {
            $tabInlinesCss .= 'background-color: ' . $tabDetails['label_bg_color'] . ';';
        }
        if (isset($tabDetails['label_bg_border_radius']) && $tabDetails['label_bg_border_radius']) {
            $tabInlinesCss .= 'border-radius: ' . $tabDetails['label_bg_border_radius'] . ';';
            $tabInlinesCss .= '-moz-border-radius: ' . $tabDetails['label_bg_border_radius'] . ';';
            $tabInlinesCss .= '-webkit-border-radius: ' . $tabDetails['label_bg_border_radius'] . ';';
        }
        if (isset($tabDetails['label_padding']) && $tabDetails['label_padding']) {
            $tabInlinesCss .= 'padding: ' . $tabDetails['label_padding'] . ';';
        }
        return $tabInlinesCss;
    }
}
