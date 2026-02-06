<?php

namespace Cfs\InvalidAccess\Block;

use Magento\Cms\Model\Template\FilterProvider;


class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $pageRepositoryInterface;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cms\Api\PageRepositoryInterface         $pageRepositoryInterface
     * @param \Magento\Framework\Api\SfearchCriteriaBuilder     $searchCriteriaBuilder
     * @param array                                            $data
     */
    protected $helper;

    protected $filter;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Widget\Model\Template\Filter $filter,
        \Cfs\InvalidAccess\Helper\Data $helper,
        array $data = []
    ) {
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->helper = $helper;
        $this->filter = $filter;
        parent::__construct($context, $data);
    }
    /**
     * Return CMS Page Details by URL Key
     * 
     * @param  string $urlKey
     * @return string
     */
    public function getCmsPageDetails()
    {
        $urlKey = $this->helper->getGeneralConfig();


        if (!empty($urlKey)) {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $urlKey, 'eq')->create();
            $pages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();

            foreach ($pages as $page) {
                $page->getContent();
            }
            return $this->filter->filter((string)$page->getContent());
        } else {
            return 'Page URL Key is invalid';
        }
    }
}
