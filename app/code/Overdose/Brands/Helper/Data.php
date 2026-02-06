<?php
namespace Overdose\Brands\Helper;

use GraphQL\Examples\Blog\Data\User;
use Magento\Framework\App\Helper\Context;
use Overdose\Brands\Model\Config\Source\UserGroup;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Cgl\Customer\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Company\Api\CompanyManagementInterface
     */
    protected $companyRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Company\Api\CompanyManagementInterface $companyRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Company\Api\CompanyManagementInterface $companyRepository,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->companyRepository = $companyRepository;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     *
     * @param null $customerId
     * @return bool
     */
    public function isB2cUserOrGuest($customerId = null): bool
    {
        if ($customerId == null) {
            $isB2cOrGuest = false;
            if (!$this->customerSession->isLoggedIn()) {
                $isB2cOrGuest = true;
            } else {
                $customerId = $this->customerSession->getCustomerId();
                $company = $this->companyRepository->getByCustomerId($customerId);
                if (!$company) {
                    $isB2cOrGuest = true;
                }
            }

            return $isB2cOrGuest;
        }

        if (in_array($this->getUserGroup($customerId), [UserGroup::GUEST, UserGroup::B2C_USER])) {
            return true;
        }

        return false;
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

    /**
     *
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getStoreConfig($path, $storeId = null) {
        if (!$storeId) {
            return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        }

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
