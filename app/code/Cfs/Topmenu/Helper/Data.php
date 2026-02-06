<?php

namespace Cfs\Topmenu\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

     const ICON_ATTRIBUTE_CODE = "icon";

     protected $categoryFactory = null;

     public function __construct(
       \Magento\Framework\App\Helper\Context $context,
       \Magento\Catalog\Model\CategoryFactory $categoryFactory
      )
      {
            parent::__construct($context);
            $this->categoryFactory =  $categoryFactory;
      }

      public function getIconByNodeId($nodeId)
      {
         $categoryId = (int) str_replace("category-node-","",$nodeId);
        
         if(!empty($categoryId)){
    
            $category = $this->categoryFactory->create();
            $category->load($categoryId);
            return $category->getData(self::ICON_ATTRIBUTE_CODE);
         }

         return null;
      }


      public function getTopCategoryTitle()
      {
        return $this->scopeConfig->getValue('catalog/frontend/top_category_title');
      }
}
