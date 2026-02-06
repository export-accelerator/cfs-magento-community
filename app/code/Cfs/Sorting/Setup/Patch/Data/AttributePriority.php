<?php

namespace Cfs\Sorting\Setup\Patch\Data;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AttributePriority implements DataPatchInterface, PatchVersionInterface
{
    
    private $moduleDataSetup;

    private $categorySetupFactory;
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }
    public function apply()
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);


        $attributeSetId = $categorySetup->getDefaultAttributeSetId(\Magento\Catalog\Model\Product::ENTITY);
        
        $attribute = $categorySetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY,'priority');
        if(!isset($attribute['attribute_id'])) {
                $categorySetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'priority',
                    [
                        'type' => 'int',
                        'label' => 'Priority',
                        'input' => 'text',
                        'required' => true,
                        'frontend_class' => 'validate-number',
                        'class' => '',
                        'sort_order' => 170,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'Product Details',
                        'visible' => true,
                        'require' => true,
                        'user_defined' => true,
                        'default' => 0,
                        'used_in_product_listing' => true,
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => false,
                        'used_for_sort_by' => true,
                        'is_filterable_in_grid' => false
                    ]
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            CreateAttributeSets::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
