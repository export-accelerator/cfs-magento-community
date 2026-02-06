<?php

namespace Overdose\Brands\Model\Product;

use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Overdose\Brands\Model\Image as BrandsImage;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory
     */
    private $brandsCollectionFactory;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var array
     */
    public $_storeManager;
    public $fileInfo;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory $brandsCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Overdose\Brands\Model\FileInfo $fileInfo,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->brandsCollectionFactory = $brandsCollectionFactory;
        $this->dataPersistor = $dataPersistor;
        $this->collection = $brandsCollectionFactory->create();
        $this->_storeManager = $storeManager;
        $this->fileInfo = $fileInfo;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }


    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $baseurl =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $items = $this->collection->getItems();

        /** @var \Overdose\Brands\Model\Product\Brands $item */
        foreach ($items as $item) {
            if($item['logo']){
                $item['logo'] = BrandsImage::BRANDS_LOGO_PATH .'/'. $item['logo'];
                if($this->fileInfo->isExist($item['logo'])){
                    $stat = $this->fileInfo->getStat($item['logo']);
                    $mime = $this->fileInfo->getMimeType($item['logo']);
                    $img = [];
                    $img[0]['image'] = $item['logo'];
                    $img[0]['name'] = basename($item['logo']);
                    $img[0]['url'] = $baseurl.$item['logo'];
                    $img[0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $img[0]['type'] = $mime;
                    $item['logo'] = $img;
                }else{
                    $item['logo'] = '';
                }
            }
            $this->loadedData[$item->getId()] = $item->getData();
        }

        $data = $this->dataPersistor->get('od_brands_brands');

        if (! empty($data)) {
            /** @var \Overdose\Brands\Model\Product\Brands $item */
            $item = $this->collection->getNewEmptyItem();
            $item->setData($data);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear('od_brands_brands');
        }

        return $this->loadedData;
    }


}
