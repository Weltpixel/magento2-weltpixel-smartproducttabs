<?php
namespace WeltPixel\SmartProductTabs\Block\Adminhtml\System\Config;

class DependeciesJsTemplate extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var string
     */
    protected $_template = 'WeltPixel_SmartProductTabs::system/config/dependencies_js.phtml';

    /**
     * DependeciesJsTemplate constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_registry = $registry;

        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->setData('template', $this->_template);
        $this->setNameInLayout('wp.smartproducttabs.dependenciesjs');

        parent::_construct();
    }
}
