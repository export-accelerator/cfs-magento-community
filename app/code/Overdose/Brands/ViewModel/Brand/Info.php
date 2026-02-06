<?php

namespace Overdose\Brands\ViewModel\Brand;

class Info implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
     */
    protected $brandsRepository;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    private $filterProvider;

    /**
     * @param \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    public function __construct(
        \Overdose\Brands\Api\BrandsRepositoryInterface $brandsRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->brandsRepository = $brandsRepository;
        $this->request = $request;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Prepare HTML content
     *
     * @return string
     */
    public function getBrandContent()
    {
        $brand = $this->getBrand();
        if($brand){
            return $this->filterProvider->getPageFilter()->filter($brand->getContent());
        }

        return null;
    }

    /**
     * Retrieve Brand
     *
     * @param int $id
     * @return \Overdose\Brands\Api\Data\BrandsInterface|null
     */
    public function getBrand()
    {
        $brandId = $this->request->getParam('id');
        $brand = null;
        try {
            $brand = $this->brandsRepository->getById($brandId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            //nothing
        }

        return $brand;
    }
}