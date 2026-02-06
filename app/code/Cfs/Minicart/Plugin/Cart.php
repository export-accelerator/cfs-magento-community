<?php

namespace Cfs\Minicart\Plugin;

class Cart {
    protected $checkoutSession;
    protected $checkoutHelper;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper
    ){
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
    }
    public function afterGetSectionData($subject, $result)
    {
        $totals = $this->checkoutSession->getQuote()->getTotals();
        $grandTotal = $totals['grand_total']->getValue();
        $tax = $totals['tax']->getValue();

        $extraInfo = [ 
            'grand_total' => isset($totals['grand_total'])
            ? $this->checkoutHelper->formatPrice($grandTotal)
            : 0,
            'tax' => isset($totals['tax'])
            ? $this->checkoutHelper->formatPrice($tax)
            : 0,
        ];
        return array_merge($result, $extraInfo);
    }
}
