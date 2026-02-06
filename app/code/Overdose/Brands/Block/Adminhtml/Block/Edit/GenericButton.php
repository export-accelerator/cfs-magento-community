<?php

namespace Overdose\Brands\Block\Adminhtml\Block\Edit;

use Overdose\Brands\Model\ResourceModel\Product\Brands as BrandsResourceModel;

class GenericButton
{
    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    private $context;

    /**
     * @var \Overdose\Brands\Model\Product\BrandsFactory
     */
    private $brandsFactory;

    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands
     */
    private $brandsResource;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Overdose\Brands\Model\Product\BrandsFactory $brandsFactory
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Overdose\Brands\Model\Product\BrandsFactory $brandsFactory,
        \Overdose\Brands\Model\ResourceModel\Product\Brands $brandsResource
    ) {
        $this->context = $context;
        $this->brandsFactory = $brandsFactory;
        $this->brandsResource = $brandsResource;
    }

    /**
     * Return Brand ID
     *
     * @return int|null
     */
    public function getBrandId()
    {
        $id = (int)$this->context->getRequest()->getParam(BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME);

        if ($id) {
            /** @var \Overdose\Brands\Model\Product\Brands $model */
            $model = $this->brandsFactory->create();
            $this->brandsResource->load($model, $id);

            if ($model->getId()) {
                return  $model->getId();
            }
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}