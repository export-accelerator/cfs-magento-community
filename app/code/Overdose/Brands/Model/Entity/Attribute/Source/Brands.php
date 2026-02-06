<?php

namespace Overdose\Brands\Model\Entity\Attribute\Source;

class Brands extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory
     */
    protected $brandsCollectionFactory;

    /**
     * @param \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory $brandsCollectionFactory
    )
    {
        $this->brandsCollectionFactory = $brandsCollectionFactory;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {

            $collection = $this->brandsCollectionFactory->create();

            $this->_options[] = ['label' => 'not selected', 'value' => 0];
            foreach ($collection->getItems() as $item) {
                $this->_options[] = ['label' => $item->getName(), 'value' => $item->getBrandId()];
            }
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
