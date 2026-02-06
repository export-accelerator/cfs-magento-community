<?php

namespace Cfs\Slider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Slider extends AbstractDb
{

    const KEY_EVATIMBER_BANNER_SLIDER_TABLE = 'cfs_slider';
    const KEY_EVATIMBER_BANNER_SLIDER_TABLE_ID = 'id';

    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            self::KEY_EVATIMBER_BANNER_SLIDER_TABLE,
            self::KEY_EVATIMBER_BANNER_SLIDER_TABLE_ID
        );
    }
}
