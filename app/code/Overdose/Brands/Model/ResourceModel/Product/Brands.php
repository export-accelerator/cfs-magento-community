<?php

namespace Overdose\Brands\Model\ResourceModel\Product;

use Magento\Framework\DB\Select;
/**
 * Class Brands
 */
class Brands extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'od_brands';

    /**
     * Name of Primary Column
     */
    const MAIN_TABLE_ID_FIELD_NAME = 'brand_id';


    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Check if brand identifier exist
     * return brand id if brand exists
     *
     * @param string $identifier
     * @return int|false
     */
    public function checkIdentifier($identifier)
    {
        $select = $this->getConnection()->select()
            ->from(['brands' => $this->getMainTable()])
            ->where('brands.identifier = ?', $identifier);

        $select->reset(Select::COLUMNS)
            ->columns('brands.brand_id')
            ->order('brands.identifier DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
}