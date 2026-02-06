<?php

namespace Cfs\Slider\Model;

use Cfs\Slider\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class SliderFormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;

    protected $_storeManager;

    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sliderCollectionFactory,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sliderCollectionFactory->create();
        $this->_storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }


    public function getData()
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
            if ($item->getImage()) {
                $m['image'][0]['name'] = $item->getImage();
                $m['image'][0]['url'] = $this->getMediaUrl() . 'slider/images/' . $item->getImage();
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $m);
            }
        }
        return $this->loadedData;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}
