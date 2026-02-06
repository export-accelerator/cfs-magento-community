<?php

namespace Overdose\Brands\Controller\Brand\Info;

use Magento\Framework\App\Action\Action;

class View extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
     */
    protected $brandsRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->pageConfig = $pageConfig;
        $this->brandsRepository = $brandsRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $brand = $this->getBrand();
        if($brand){
            $resultPage->getConfig()->getTitle()->set($brand->getName());
            $resultPage->getConfig()->setMetaTitle($brand->getMetaTitle());
            $resultPage->getConfig()->setDescription($brand->getMetaDescription());

            /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbs */
            $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbs) {
                $breadcrumbs->addCrumb('home', [
                        'label' => __('Home'),
                        'title' => __('Home'),
                        'link' => $this->_url->getUrl('')
                    ]
                );
                $breadcrumbs->addCrumb('brands', [
                        'label' => __('Brands'),
                        'title' => __('Brands'),
                        'link' => $this->_url->getUrl('brands')
                    ]
                );
                $breadcrumbs->addCrumb('custom_module', [
                        'label' => $brand->getName(),
                        'title' => $brand->getName()
                    ]
                );
            }
        }

        return $resultPage;
    }

    /**
     * Retrieve Brand
     *
     * @return \Overdose\Brands\Api\Data\BrandsInterface|null
     */
    public function getBrand()
    {
        $brandId = $this->_request->getParam('id');
        $brand = null;
        try {
            $brand = $this->brandsRepository->getById($brandId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            //nothing
        }

        return $brand;
    }
}
