<?php

namespace Overdose\Brands\Model\Config\Source;

class UserGroup implements \Magento\Framework\Option\ArrayInterface
{
    const GUEST = 1;
    const B2C_USER = 2;
    const B2B_USER = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GUEST, 'label' => __('Guest')],
            ['value' => self::B2C_USER, 'label' => __('B2C Customer')],
            ['value' => self::B2B_USER, 'label' => __('B2B Customer')],
        ];
    }
}
