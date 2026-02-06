<?php

namespace Overdose\Brands\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;


class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BRANDS_LOGO_PATH = 'brands/logo';
    const BRANDS_LOGO_PATH_RESIZED = 'brands/logo/resized';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
//     */
//    public function __construct(
//        \Magento\Framework\App\Helper\Context $context,
//        \Magento\Framework\ObjectManagerInterface $objectManager,
//        \Magento\Store\Model\StoreManagerInterface $storeManager,
//        \Magento\Framework\Filesystem $filesystem
//    ) {
//        parent::__construct($context);
//
//        $this->_mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
//        $this->_objectManager = $objectManager;
//        $this->_storeManager = $storeManager;
//    }
//
//    /**
//     * get media url of image.
//     *
//     * @param string $imagePath
//     *
//     * @return string
//     */
//    public function getMediaUrlImage($imagePath = '')
//    {
//        return $this->_storeManager->getStore()
//                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $imagePath;
//    }
}
