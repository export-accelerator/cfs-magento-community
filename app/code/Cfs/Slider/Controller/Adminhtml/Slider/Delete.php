<?php

namespace Cfs\Slider\Controller\Adminhtml\Slider;

class Delete extends \Magento\Backend\App\Action
{

    public $sliderFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Cfs\Slider\Model\SliderFactory $sliderFactory
    ) {
        $this->sliderFactory = $sliderFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $bannerId = (int)$this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($bannerId && (int) $bannerId > 0) {
            try {
                $model =  $this->sliderFactory->create();
                $model->load($bannerId);
                $model->delete();
                $this->messageManager->addSuccess(__('The Banner has been deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addError(__('Banner doesn\'t exist any longer.'));
        return $resultRedirect->setPath('*/*/index');
    }
}
