<?php

namespace Overdose\Brands\ViewModel;

use Overdose\Brands\Model\Image as BrandsImage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Overdose\Brands\Model\Product\Brands as BrandsModel;

class Brands implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Product repository
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Category list
     *
     * @var \Magento\Catalog\Api\CategoryListInterface
     */
    protected $categoryList;

    /**
     * @var \Magento\Catalog\Helper\Output $helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Filesystem $filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory $imageFactory
     */
    protected $_imageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
     */
    protected $brandsRepository;
    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /** @var \Overdose\Brands\Api\Data\BrandsInterface[] */
    protected $brands = [];

    /** @var \Magento\Catalog\Api\Data\ProductInterface[] */
    protected $products = [];

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Output $helper
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Output $helper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->brandsRepository = $brandsRepository;
        $this->request = $request;
    }

    /**
     * @param int|null
     * @param int|null
     * @param int|null
     * @return string
     */
    public function getBrandLogoHtml($productId = null, $width = null, $height = null)
    {
        $html = '';
        $product = $this->getProduct($productId);
        if ($product) {
            $imgUrl = $this->getBrandLogoByProduct($product, $width, $height);
            if ($imgUrl) {
                $brandName = $this->getBrandNameByProduct($product);
                $html = '<img src="' . $imgUrl . '" alt="' . $brandName . '" title="' . $brandName . '">';
            }
        }
        return $html;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface
     * @param int|null
     * @param int|null
     * @return string|null
     */
    public function getBrandLogoByProduct($product, $width = null, $height = null)
    {
        $brand = $this->getBrand($product);
        if ($brand) {
            if ($logo = $brand->getLogo()) {
                $img = $this->getLogoUrl($logo, $width, $height);
                return $img;
            }
        }

        return null;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface
     * @return string|null
     */
    public function getBrandNameByProduct($product)
    {
        $brand = $this->getBrand($product);
        if ($brand) {
            return $brand->getName();
        }

        return null;
    }

    /**
     * @param int
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function getProduct($productId = null)
    {
        if (!$productId) {
            $productId = $this->request->getParam('id');
        }

        if ($productId) {
            if (!isset($this->products[$productId]) || !array_key_exists($productId, $this->products)) { // faster than just "array_key_exists"
                try {
                    $this->products[$productId] = $this->productRepository->getById($productId);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->products[$productId] = null;
                }
            }
            return $this->products[$productId];
        }
        return null;
    }

    /**
     * @param null $product
     * @return string
     */
    public function getBrandName($product = null)
    {
        if ($product === null) {
            $product = $this->getProduct();
        }

        if ($product) {
            $brand = $this->getBrand($product);
            return $brand ? $brand->getName() : '';
        }

        return '';
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface
     * @return \Overdose\Brands\Api\Data\BrandsInterface|null
     */
    public function getBrand($product)
    {
        $brandId = $this->helper->productAttribute($product, $product->getData(BrandsModel::BRAND_ATTRIBUTE_CODE), BrandsModel::BRAND_ATTRIBUTE_CODE);
        if (!isset($this->brands[$brandId]) || !array_key_exists($brandId, $this->brands)) { // faster than just "array_key_exists"
            try {
                $brand = $this->brandsRepository->getById($brandId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $brand = null;
            }

            $this->brands[$brandId] = $brand;
        }
        return $this->brands[$brandId];
    }

    /**
     * @param string
     * @param integer
     * @param integer
     * @return string|null
     */
    public function getLogoUrl($image, $width = null, $height = null)
    {
        if (empty($image)) return null;

        $absolutePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath(BrandsImage::BRANDS_LOGO_PATH) . '/' . $image;
        if (file_exists($absolutePath)) {
            if ($width) {
                $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath(BrandsImage::BRANDS_LOGO_PATH_RESIZED . '/' . $width . $height . '/') . $image;
                if (!file_exists($imageResized)) {
                    $imageResize = $this->_imageFactory->create();
                    $imageResize->open($absolutePath);
                    $imageResize->constrainOnly(TRUE);
                    $imageResize->keepTransparency(TRUE);
                    $imageResize->keepFrame(FALSE);
                    $imageResize->keepAspectRatio(TRUE);
                    $imageResize->resize($width, $height);
                    $destination = $imageResized;
                    $imageResize->save($destination);
                }
                $path = BrandsImage::BRANDS_LOGO_PATH_RESIZED . '/' . $width . $height . '/' . $image;
            } else {
                $path = BrandsImage::BRANDS_LOGO_PATH . '/' . $image;
            }

            return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
        }

        return null;
    }
}
