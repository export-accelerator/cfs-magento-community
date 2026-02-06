<?php

namespace Cfs\DataMigration\Model\Entity\Attribute\Source;

class Umo extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const OPTION_EACH = "EA";
    const OPTION_METER = "MTR";
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            
            $this->_options[] = ['label' => __(self::OPTION_EACH), 'value' => self::OPTION_EACH];
            $this->_options[] = ['label' => __(self::OPTION_METER), 'value' => self::OPTION_METER];
            
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
