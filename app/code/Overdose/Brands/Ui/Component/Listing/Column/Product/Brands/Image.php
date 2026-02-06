<?php

namespace Overdose\Brands\Ui\Component\Listing\Column\Product\Brands;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Overdose\Brands\Model\Image as BrandsImage;

class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * default width and height image.
     */
    const IMAGE_WIDTH = '100px';
    const IMAGE_HEIGHT = 'auto';
    const IMAGE_STYLE = 'display: block; margin: auto;';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $this->_prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * prepare item.
     *
     * @param array $item
     *
     * @return array
     */
    protected function _prepareItem(array & $item)
    {
        $width = $this->hasData('width') ? $this->getWidth() : self::IMAGE_WIDTH;
        $height = $this->hasData('height') ? $this->getHeight() : self::IMAGE_HEIGHT;
        $style = $this->hasData('style') ? $this->getStyle() : self::IMAGE_STYLE;

        if (isset($item[$this->getData('name')])) {
            if ($item[$this->getData('name')]) {
                $mediaPath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $srcImage = $mediaPath . BrandsImage::BRANDS_LOGO_PATH .'/'. $item[$this->getData('name')];
                $item[$this->getData('name')] = sprintf(
                    '<img src="%s"  width="%s" height="%s" style="%s" />',
                    $srcImage,
                    $width,
                    $height,
                    $style
                );
            } else {
                $item[$this->getData('name')] = '';
            }
        }

        return $item;
    }
}
