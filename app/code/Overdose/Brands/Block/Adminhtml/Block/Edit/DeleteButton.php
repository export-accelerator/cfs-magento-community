<?php

namespace Overdose\Brands\Block\Adminhtml\Block\Edit;

use Overdose\Brands\Model\ResourceModel\Product\Brands as BrandsResourceModel;

/**
 * Class DeleteButton
 */
class DeleteButton extends \Overdose\Brands\Block\Adminhtml\Block\Edit\GenericButton
    implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->getBrandId()) {
            $data = [
                'label' => __('Delete Brand'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * URL to send delete requests to.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete',
            [
                BrandsResourceModel::MAIN_TABLE_ID_FIELD_NAME => $this->getBrandId(),
            ]
        );
    }
}