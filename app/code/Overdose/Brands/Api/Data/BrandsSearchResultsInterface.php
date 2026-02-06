<?php

namespace Overdose\Brands\Api\Data;


interface BrandsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get rules.
     *
     * @return \Overdose\Brands\Api\Data\BrandsInterface[]
     */
    public function getItems();

    /**
     * Set rules .
     *
     * @param \Overdose\Brands\Api\Data\BrandsInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
