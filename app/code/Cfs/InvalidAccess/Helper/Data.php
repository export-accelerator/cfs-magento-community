<?php

namespace Cfs\InvalidAccess\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function getConfigValue($field =null, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGeneralConfig($code = null, $storeId = null)
    {
        return $this->getConfigValue('web/default/cms_restrict_menu' . $code, $storeId);
    }
}
