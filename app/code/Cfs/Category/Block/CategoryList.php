<?php

namespace Cfs\Category\Block;

use Magento\Framework\View\Element\Template;

class CategoryList extends Template
{
    protected $_storeManager;

    protected $_categoryCollection;

    public function __construct(
        Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $categories = $this->_categoryCollection->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('is_active', 1)
            ->addAttributeToFilter('level', array('eq' => 3));

        return $categories;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()

            ->getBaseUrl();

        return $mediaUrl;
    }
}
