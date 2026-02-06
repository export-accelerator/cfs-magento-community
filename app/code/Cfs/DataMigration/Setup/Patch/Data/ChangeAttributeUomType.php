<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cfs\DataMigration\Setup\Patch\Data;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class AttributeItemNumberId
 * @package Cfs\DataMigration\Setup\Patch
 */
class ChangeAttributeUomType implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * PatchInitial constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory,
        ResourceConnection $resource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function apply()
    {
        /** @var CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);


        $attributeSetId = $categorySetup->getDefaultAttributeSetId(\Magento\Catalog\Model\Product::ENTITY);
        
        $attribute = $categorySetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY,'cgl_uom');
        if(isset($attribute['attribute_id']) && $attribute['attribute_id'] > 0 ) {

            $categorySetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, $attribute['attribute_id'],'backend_type',"varchar",null);
            $sourceClass = \Cfs\DataMigration\Model\Entity\Attribute\Source\Umo::class;
            $categorySetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, $attribute['attribute_id'],'source_model',$sourceClass,null);
            
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            AttributeUom::class,
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
