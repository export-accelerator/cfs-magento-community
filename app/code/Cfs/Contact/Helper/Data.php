<?php

namespace Cfs\Contact\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
     public function __construct(
       \Magento\Framework\App\Helper\Context $context
      )
      {
            parent::__construct($context);
      }

      public function getFormId()
      {
        return $this->scopeConfig->getValue('contact/contact/amasty_form_id');
      }
}
