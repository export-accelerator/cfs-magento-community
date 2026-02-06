<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cfs\Sorting\Setup\Patch\Data;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;

/**
 * Class CreateAttributeSets
 * @package Cfs\DataMigration\Setup\Patch
 */
class CreateAttributeSets implements DataPatchInterface, PatchVersionInterface
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
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var AttributeSetCollectionFactory
     */
    private $attributeSetCollectionFactory;

    /**
     * PatchInitial constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeSetFactory $attributeSetFactory
     * @param AttributeSetCollection $attributeSetCollection
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeSetFactory $attributeSetFactory,
        AttributeSetCollectionFactory $attributeSetCollectionFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function apply()
    {
        /** @var CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        
        $defaultAttributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        
        $attributeSetsNames = ['CFS_Tooling', 'CFS_Refuelling', 'CFS_Power-Motion-Flow-Control', 'CFS_Lubrication', 'CFS_Industrial',
                          'CFS_Hydraulic', 'CFS_Fluid-Dispensing', 'CFS_Fire-Suppression', 'CFS_Dust-Suppression', 'CFS_Chemicals-Fuels-Lubricants'
                         ];

        $sortOrder = 200;

        foreach($attributeSetsNames as $attributeSetName){
            
            $attributeSetCollection = $this->attributeSetCollectionFactory->create()->addFieldToSelect('attribute_set_id')
                                     ->addFieldToFilter('attribute_set_name', $attributeSetName)
                                     ->getFirstItem()->toArray();

            $attributeSetId = isset($attributeSetCollection['attribute_set_id']) ? (int) $attributeSetCollection['attribute_set_id'] : 0;
             
            if($attributeSetId > 0) {
                continue;
             }

            $attributeSet = $this->attributeSetFactory->create();

            $data = [
                'attribute_set_name' => $attributeSetName,
                'entity_type_id' => $entityTypeId,
                'sort_order' => $sortOrder,
            ];

            $sortOrder += 10;

            $attributeSet->setData($data);
            $attributeSet->validate();
            $attributeSet->save();
            $attributeSet->initFromSkeleton($defaultAttributeSetId);
            $attributeSet->save();
        }
        
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
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
