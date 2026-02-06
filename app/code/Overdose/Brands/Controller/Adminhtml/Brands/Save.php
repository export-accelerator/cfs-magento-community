<?php

namespace Overdose\Brands\Controller\Adminhtml\Brands;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Overdose\Brands\Model\ResourceModel\Product\Brands as BrandsResourceModel;

/**
 * Class Save
 */
class Save extends \Overdose\Brands\Controller\Adminhtml\Brands
    implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Overdose\Brands\Model\Product\BrandsFactory
     */
    private $brandsFactory;

    /**
     * @var \Overdose\Brands\Api\BrandsRepositoryInterface
     */
    private $brandsRepository;

    /**
     * Image uploader
     *
     * @var \Overdose\Brands\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Overdose\Brands\Model\Product\BrandsFactory $brandsFactory
     * @param \Overdose\Brands\Model\BrandsRepository $BrandsRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Overdose\Brands\Model\Product\BrandsFactory $brandsFactory,
        \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository,
        \Overdose\Brands\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->brandsFactory = $brandsFactory;
        $this->brandsRepository = $brandsRepository;
        $this->imageUploader = $imageUploader;
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
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            if (empty($data[BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME])) {
                $data[BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME] = null;
            }

            /** @var \Overdose\Brands\Model\Product\Brands $model */
            $model = $this->brandsFactory->create();
            $id = $this->getRequest()->getParam(BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME);

            if ($id) {
                try {
                    $model = $this->brandsRepository->getById($id, true);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('This product Brands no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            if (isset($data['logo'][0]['name']) && (isset($data['logo'][0]['tmp_name']) || isset($data['logo'][0]['url']))) {
                $sourcePatch = null;
                if(isset($data['logo'][0]['url']) && !isset($data['logo'][0]['tmp_name'])){
                    $url = $data['logo'][0]['url'];
                    $url = str_replace('/pub/media', '' , $url);
                    $url = str_replace($data['logo'][0]['name'], '' , $url);
                    $path= trim($url , '/');
                    $sourcePatch = $path;
                }

                $data['logo'] = $data['logo'][0]['name'];
                try {
                    $this->imageUploader->copyFileToBrandsDir($data['logo'], $sourcePatch);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {

                }

            } elseif (isset($data['logo'][0]['image']) && !isset($data['logo'][0]['tmp_name'])) {
                $data['logo'] = basename($data['logo'][0]['image']);;
            } else {
                $data['logo'] = '';
            }

            
            if (isset($data['store_ids'])) {
                $data['store_ids'] = join(',',$data['store_ids']);
            }

            $model->setData($data);

            try {
                $this->brandsRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the product Brands.'));
                $this->dataPersistor->clear('od_brands_brands');

                return $this->processReturn($resultRedirect, $model, $data);
            } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the brand.'));
            }

            $this->dataPersistor->set('od_brands_brands', $data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME => $id,
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirect
     * @param \Overdose\Brands\Model\Product\Brands $model
     * @param $data
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function processReturn(
        \Magento\Framework\Controller\Result\Redirect $resultRedirect,
        \Overdose\Brands\Model\Product\Brands $model,
        $data
    ) {
        $redirect = isset($data['back']) ? $data['back'] : 'close';

        if ('duplicate' === $redirect) {
            $newBrand = $this->brandsFactory->create(['data' => $data]);
            $newBrand->setId(null);
            $identifier = $model->getIdentifier() . '-' . uniqid();
            $newBrand->setIdentifier($identifier);
            $this->brandsRepository->save($newBrand);
            $this->messageManager->addSuccessMessage(__('You duplicated the brand.'));
            $resultRedirect->setPath(
                '*/*/edit',
                [
                    BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME => $newBrand->getId(),
                    '_current' => true
                ]
            );
        } else if ('continue' === $redirect) {
            $resultRedirect->setPath('*/*/edit', [BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME => $model->getId()]);
        } else if ( 'close' === $redirect) {
            $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
