<?php
namespace Overdose\Brands\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Overdose\Brands\Model\Product\Brands as BrandsModel;

class Brands extends Template implements BlockInterface
{
    protected $_template = "widget/brands.phtml";
    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory
     */
    protected $brandsCollectionFactory;

    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context,
     * @param \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory $brandsCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Overdose\Brands\Model\ResourceModel\Product\Brands\CollectionFactory $brandsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []

    )
    {
        $this->brandsCollectionFactory = $brandsCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get brands list
     *
     * @param int
     * @return array
     */
    public function getBrandsList()
    {
        $categoryId = $this->getData('categoryId');
        $brandList = array();
        $collection = $this->brandsCollectionFactory->create();
        $collection->addStoreFilter($this->storeManager->getStore()->getId(),true);
        $collection->addFieldToFilter('is_featured', array('eq'=> 1));
        $collection->setOrder('position', $collection::SORT_ORDER_ASC);
        if($categoryId){
            $category = $this->getCategoryById((int) $categoryId);
            if($category){
                $productCollection = $this->productCollectionFactory->create();
                $productCollection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
                $productCollection->addCategoryFilter($category);
                $productCollection->addAttributeToSelect(BrandsModel::BRAND_ATTRIBUTE_CODE);
                $productCollection->groupByAttribute(BrandsModel::BRAND_ATTRIBUTE_CODE);
                $brandIds = array_column($productCollection->toArray(), BrandsModel::BRAND_ATTRIBUTE_CODE);
                if(count($brandIds)){
                    $collection->addFieldToFilter('brand_id', array('in'=>implode(',', $brandIds)));
                }else{
                    return $brandList;
                }
            }else{
                return $brandList;
            }
        }

        foreach ($collection->getItems() as $item) {
            $brandList [] = [
                'name' => $item->getName(),
                'logo_url' => $item->getLogoUrl(),
                'url' => $item->getUrl()];
        }

        return $brandList;
    }

    public function checkIfCategoryIdExists()
    {
        $categoryId = $this->getData('categoryId');
        return ($categoryId) ? true : false;
    }

    /**
     * Get category by id
     *
     * @param int
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    private function getCategoryById($categoryId)
    {
        try {
            $category = $this->categoryRepository->get($categoryId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $category = null;
        }

        return $category;
    }
}
