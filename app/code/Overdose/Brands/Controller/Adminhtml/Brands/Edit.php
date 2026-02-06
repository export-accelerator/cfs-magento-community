<?php

namespace Overdose\Brands\Controller\Adminhtml\Brands;

use Overdose\Brands\Model\ResourceModel\Product\Brands as BrandsResourceModel;

/**
 * Class Edit
 */
class Edit extends \Overdose\Brands\Controller\Adminhtml\Brands
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Overdose\Brands\Model\Product\BrandsFactory
     */
    private $brandsFactory;

    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands
     */
    private $brandsResource;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Overdose\Brands\Model\Product\BrandsFactory $brandsFactory
     * @param \Overdose\Brands\Model\ResourceModel\Product\Brands $brandsResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Overdose\Brands\Model\Product\BrandsFactory $brandsFactory,
        \Overdose\Brands\Model\ResourceModel\Product\Brands $brandsResource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->brandsFactory = $brandsFactory;
        $this->brandsResource = $brandsResource;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Brands'));

        /** @var \Overdose\Brands\Model\Product\Brands $model */
        $model = $this->brandsFactory->create();
        $id = $this->getRequest()->getParam(BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME);

        if ($id) {
            $this->brandsResource->load($model, $id);

            if (! $model->getId()) {
                $this->messageManager->addErrorMessage(__('This product brand no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit') : __('New'),
            $id ? __('Edit') : __('New')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Brands'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Brand'));

        return $resultPage;
    }
}
