<?php

namespace Overdose\Brands\Controller\Adminhtml\Brands;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Overdose\Brands\Model\ResourceModel\Product\Brands as BrandsResourceModel;

/**
 * Class Delete
 */
class Delete extends \Overdose\Brands\Controller\Adminhtml\Brands
    implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Overdose\Brands\Model\BrandsRepository
     */
    private $brandsRepository;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Overdose\Brands\Model\BrandsRepository $brandsRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
    ) {
        $this->brandsRepository = $brandsRepository;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam(BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME);

        if ($id) {
            try {
                $this->brandsRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the brand.'));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                return $resultRedirect->setPath('*/*/*');
            } catch (\Magento\Framework\Exception\CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the brand.'));
                return $resultRedirect->setPath('*/*/*');
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a brand to delete.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
