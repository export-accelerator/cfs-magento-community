<?php

namespace Cfs\Slider\Block;

use Cfs\Slider\Model\ResourceModel\Slider\CollectionFactory;

class Index extends \Magento\Framework\View\Element\Template
{
	public $collection;
	protected $bannerFactory;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		CollectionFactory $collectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Cfs\Slider\Model\SliderFactory $sliderFactory
	) {
		$this->collection = $collectionFactory;
		$this->sliderFactory = $sliderFactory;
		$this->_storeManager = $storeManager;
		parent::__construct($context);
	}

	public function getSliders()
	{
		$collection = $this->sliderFactory->create()->getCollection()->addFieldToFilter(
			'status',
			1
		)->setOrder('main_table.position', 'ASC');;
		return $collection;
	}
	public function getSliderBaseUrl($imageName)
	{
		$path = $this->_storeManager->getStore()->getBaseUrl('media');
		return $path . 'slider/images/' . $imageName;
	}
}
