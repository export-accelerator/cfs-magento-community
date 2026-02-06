<?php

namespace Overdose\Brands\Model\Product;

use Overdose\Brands\Model\Image as BrandsImage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

/**
 * Class Product Brands
 */
class Brands extends \Magento\Framework\Model\AbstractModel implements \Overdose\Brands\Api\Data\BrandsInterface
{
    const BRAND_ATTRIBUTE_CODE = 'cgl_brand';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    protected $_filesystem ;
    protected $_imageFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Overdose\Brands\Model\ResourceModel\Product\Brands::class);
    }

    /**
     * Retrieve Name
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->getData(self::KEY_NAME);
    }
    /**
     * Set Name
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setName($value)
    {
        $this->setData(self::KEY_NAME, (string)$value);
        return $this;
    }

    /**
     * Retrieve Logo
     *
     * @return string
     */
    public function getLogo()
    {
        return (string)$this->getData(self::KEY_LOGO);
    }
    /**
     * Set Logo
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setLogo($value)
    {
        $this->setData(self::KEY_LOGO, (string)$value);
        return $this;
    }

    /**
     * Retrieve Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return (string)$this->getData(self::KEY_IDENTIFIER);
    }
    /**
     * Set Identifier
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setIdentifier($value)
    {
        $this->setData(self::KEY_IDENTIFIER, (string)$value);
        return $this;
    }

    /**
     * Retrieve Content
     *
     * @return string
     */
    public function getContent()
    {
        return (string)$this->getData(self::KEY_CONTENT);
    }
    /**
     * Set Content
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setContent($value)
    {
        $this->setData(self::KEY_CONTENT, (string)$value);
        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getMetaTitle()
    {
        return $this->getData(self::KEY_META_TITLE);
    }

    /**
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setMetaTitle($value)
    {
        $this->setData(self::KEY_META_TITLE, (string)$value);
        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getMetaDescription()
    {
        return $this->getData(self::KEY_META_DESCRIPTION);
    }

    /**
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setMetaDescription($value)
    {
        $this->setData(self::KEY_META_DESCRIPTION, (string)$value);
        return $this;
    }

    public function getLogoUrl($width = null, $height = null)
    {
        $image = $this->getLogo();
        if($image){
            $absolutePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath(BrandsImage::BRANDS_LOGO_PATH) . '/' . $image;
            if (file_exists($absolutePath)){
                if($width){
                    $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                            ->getAbsolutePath(BrandsImage::BRANDS_LOGO_PATH_RESIZED . '/' . $width . $height . '/') . $image;
                    if (!file_exists($imageResized)) {
                        $imageResize = $this->_imageFactory->create();
                        $imageResize->open($absolutePath);
                        $imageResize->constrainOnly(TRUE);
                        $imageResize->keepTransparency(TRUE);
                        $imageResize->keepFrame(FALSE);
                        $imageResize->keepAspectRatio(TRUE);
                        $imageResize->resize($width,$height);
                        $destination = $imageResized ;
                        $imageResize->save($destination);
                    }
                    $path = BrandsImage::BRANDS_LOGO_PATH_RESIZED . '/' . $width . $height . '/' .$image;
                }else{
                    $path = BrandsImage::BRANDS_LOGO_PATH . '/'. $image;
                }

                return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
            }
        }

        return null;
    }

    public function getUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(). 'brands/' . $this->getIdentifier();
    }
}
