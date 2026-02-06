<?php

namespace Cfs\Slider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;


class Save extends \Magento\Backend\App\Action
{


    protected $uiExamplemodel;
    protected $sliderFactory;
    protected $sliderResourceModel;
    protected $resultRedirectFactory;
    protected $imageUploader;
    protected $adminsession;
    protected $bannerFactory;
    protected $bannerResourceModel;
    /**
     * @param Action\Context $context
     * @param Blog           $uiExamplemodel
     * @param Session        $adminsession
     */
    public function __construct(
        Action\Context $context,
        \Cfs\Slider\Model\SliderFactory $sliderFactory,
        \Cfs\Slider\Model\ResourceModel\Slider $sliderResourceModel,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        Session $adminsession,
        \Cfs\Slider\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->sliderFactory = $sliderFactory;
        $this->sliderResourceModel = $sliderResourceModel;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->adminsession = $adminsession;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Save blog record action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirectFactory = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();
        // print_r($postData); die(__FILE__);

        $image = (isset($postData['image']['0']['name'])) ? $postData['image']['0']['name'] : null;

        if ($image !== null) {
            try {

                $imageUploader = $this->imageUploader;
                if (isset($postData['image']['0']['tmp_name'])) {
                    $imageUploader->moveFileFromTmp($image);
                }
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
            }
        }

        $postData['image'] = $image;

        $data = [
            'status' => $postData['status'],
            'title' => $postData['title'],
            // 'description' => $postData['description'],
            'url' => $postData['url'],
            'position' => $postData['position'],
            'image' => $postData['image']
        ];

        $blog_id = $this->getRequest()->getParam('id');
        if ($data) {
            $userData = $this->sliderFactory->create();
            if ($blog_id) {
                $userData->load($blog_id);
                $data = [
                    'id' => $blog_id,
                    'status' => $postData['status'],
                    'title' => $postData['title'],
                    // 'description' => $postData['description'],
                    'url' => $postData['url'],
                    'position' => $postData['position'],
                    'image' => $postData['image']

                ];
            }
            $userData->setData($data);
            try {
                $this->sliderResourceModel->save($userData);
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return   $resultRedirectFactory->setPath('*/*/add');
                    } else {
                        return   $resultRedirectFactory->setPath('*/*/edit', ['id' => $userData->getBlogId(), '_current' => true]);
                    }
                }
                return   $resultRedirectFactory->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                die($e);
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }
            $this->_getSession()->setFormData($data);
            return   $resultRedirectFactory->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return   $resultRedirectFactory->setPath('*/*/');
    }
}
