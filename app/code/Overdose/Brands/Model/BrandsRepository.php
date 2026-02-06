<?php

namespace Overdose\Brands\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BrandsRepository
 */
class BrandsRepository implements \Overdose\Brands\Api\BrandsRepositoryInterface
{
    /**
     * @var \Overdose\Brands\Model\Product\BrandsFactory
     */
    private $modelFactory;

    /**
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands
     */
    private $modelResource;

    /**
     * Internal Cache
     *
     * @var \Overdose\Brands\Model\Product\Brands[] array
     */
    protected $instancesById = [];

    /**
     * BrandsRepository constructor.
     *
     * @param \Overdose\Brands\Model\Product\BrandsFactory $modelFactory
     * @param \Overdose\Brands\Model\ResourceModel\Product\Brands $modelResource
     */
    public function __construct(
        \Overdose\Brands\Model\Product\BrandsFactory $modelFactory,
        \Overdose\Brands\Model\ResourceModel\Product\Brands $modelResource
    ) {
        $this->modelFactory = $modelFactory;
        $this->modelResource = $modelResource;
    }

    /**
     * Retrieve Product Brand
     *
     * @param int $id
     * @param bool $forceLoad
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id, $forceLoad = false)
    {
        if ($forceLoad || ! isset($this->instancesById[$id])) {
            $model = $this->modelFactory->create();
            $this->modelResource->load($model, $id);

            if (! $model->getId()) {
                throw new NoSuchEntityException(__('The brand with the "%1" ID doesn\'t exist.', $id));
            }

            $this->instancesById[$id] = $model;
        }

        return $this->instancesById[$id];
    }

    /**
     * Save Brand
     *
     * @param \Overdose\Brands\Api\Data\BrandsInterface $model
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Overdose\Brands\Api\Data\BrandsInterface $model)
    {
        try {
            $this->modelResource->save($model);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        $this->removeFromCache($model->getId());

        return $model;
    }

    /**
     * Delete Brand
     *
     * @param \Overdose\Brands\Api\Data\BrandsInterface $model
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Overdose\Brands\Api\Data\BrandsInterface $model)
    {
        try {
            $this->modelResource->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        $this->removeFromCache($model->getId());

        return true;
    }

    /**
     * Delete Brand by ID
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * Remove brand from internal cache
     *
     * @param $id
     * @return $this
     */
    public function removeFromCache($id)
    {
        if (isset($this->instancesById[$id])) {
            unset($this->instancesById[$id]);
        }

        return $this;
    }

    /**
     * Clean internal brand cache
     *
     * @return $this
     */
    public function cleanCache()
    {
        $this->instancesById = null;

        return $this;
    }
}
