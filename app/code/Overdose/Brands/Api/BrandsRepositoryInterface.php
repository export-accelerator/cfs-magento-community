<?php

namespace Overdose\Brands\Api;

/**
 * Interface BrandsRepositoryInterface
 * @package Overdose\Brands\Api
 */
interface BrandsRepositoryInterface
{
    /**
     * Retrieve Brand
     *
     * @param int $id
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Save Brand
     *
     * @param \Overdose\Brands\Api\Data\BrandsInterface $model
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Overdose\Brands\Api\Data\BrandsInterface $model);

    /**
     * Delete Brand
     *
     * @param \Overdose\Brands\Api\Data\BrandsInterface $model
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Overdose\Brands\Api\Data\BrandsInterface $model);

    /**
     * Delete Brand by ID
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}
