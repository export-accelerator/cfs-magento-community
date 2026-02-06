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
        try {
            $totals = $this->checkoutSession->getQuote()->getTotals();
            $grandTotal = isset($totals['grand_total']) ? $totals['grand_total']->getValue() : 0;
            $tax = isset($totals['tax']) ? $totals['tax']->getValue() : 0;

            $extraInfo = [ 
                'grand_total' => isset($totals['grand_total'])
                    ? $this->checkoutHelper->formatPrice($grandTotal)
                    : 0,
                'tax' => isset($totals['tax'])
                    ? $this->checkoutHelper->formatPrice($tax)
                    : 0,
            ];
            return array_merge($result, $extraInfo);
        } catch (\Exception $e) {
            // Return original result if there's an error processing totals
            return $result;
        }
    }
}
