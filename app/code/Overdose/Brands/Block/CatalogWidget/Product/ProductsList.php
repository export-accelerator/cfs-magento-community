<?php

namespace Overdose\Brands\Block\CatalogWidget\Product;

use Overdose\Brands\Model\Config\Source\UserGroup;
/**
 * Class ProductsList
 * @package Overdose\Brands\Block\CatalogWidget\Product
 */

 use Magento\Customer\Api\CustomerRepositoryInterface;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    protected $customerSession;

    protected $customerRepository;

    protected $companyRepository;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Overdose\Brands\ViewModel\Brands $brandsView,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        \Overdose\Brands\Helper\Data $customerHelper,

        

        array $data = [],
        $json = null,
        $layoutFactory = null,
        $urlEncoder = null
    ) {
        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder, $rule, $conditionsHelper, $data, $json, $layoutFactory, $urlEncoder);
        $this->setData('viewModelBrand', $brandsView);
        $this->setData('customerHelper', $customerHelper);
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKey = parent::getCacheKeyInfo();
        $cacheKey[] = 'USER_GROUP' . $this->getData('customerHelper')->getUserGroup();
        return $cacheKey;
    }

    /**
     * @param null $customerId
     * @return int
     */
    public function getUserGroup($customerId = null): int
    {
        if (!$customerId) {
            if (!$this->customerSession->isLoggedIn()) {
                return UserGroup::GUEST;
            }

            $customerId = $this->customerSession->getCustomerId();
            $company = $this->companyRepository->getByCustomerId($customerId);
            if ($company) {
                return UserGroup::B2B_USER;
            }

            return UserGroup::B2C_USER;
        }
        try {
            if (is_object($customerId)) {
                $customer = $customerId;
            } else {
                $customer = $this->customerRepository->getById($customerId);
            }

            if ($customer->getId()) {
                $company = $this->companyRepository->getByCustomerId($customer->getId());
                if ($company) {
                    return UserGroup::B2B_USER;
                }

                return UserGroup::B2C_USER;
            }

            return UserGroup::GUEST;
        } catch (NoSuchEntityException | LocalizedException $e) {
            return UserGroup::GUEST;
        }
    }

}
