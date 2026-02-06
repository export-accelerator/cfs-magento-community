<?php

namespace Cfs\Slider\Model;

use Magento\Framework\Model\AbstractModel;


class Slider extends AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cfs_slider';

    /**
     * @var string
     */
    protected $_eventObject = 'slider';

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Cfs\Slider\Model\ResourceModel\Slider');
    }
}
