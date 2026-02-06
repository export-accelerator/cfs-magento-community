<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Company\Plugin\Customer\Api\CustomerRepository;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Company\Model\Customer\CompanyAttributes;
use Magento\Company\Model\Customer\ValidateCustomerEmailAddress;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * A plugin for customer save operation for processing company routines.
 */
class Save
{
    /**
     * @var CompanyAttributes
     */
    private $customerSaveAttributes;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var ValidateCustomerEmailAddress
     */
    private $customerEmailAddressValidator;

    /**
     * Constructor
     *
     * @param CompanyAttributes $customerSaveAttributes
     * @param CompanyRepositoryInterface $companyRepository
     * @param ValidateCustomerEmailAddress $customerEmailAddressValidator
     */
    public function __construct(
        CompanyAttributes $customerSaveAttributes,
        CompanyRepositoryInterface $companyRepository,
        ValidateCustomerEmailAddress $customerEmailAddressValidator
    ) {
        $this->customerSaveAttributes = $customerSaveAttributes;
        $this->companyRepository = $companyRepository;
        $this->customerEmailAddressValidator = $customerEmailAddressValidator;
    }

    /**
     * Before customer save - update company attributes and validate customer email address
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface $customer
     * @param null $passwordHash [optional]
     * @return array
     * @throws LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $this->customerEmailAddressValidator->execute();
        $this->customerSaveAttributes->updateCompanyAttributes($customer);
        $customer = $this->setCustomerGroup($customer);
        return [$customer, $passwordHash];
    }

    /**
     * Set customer group.
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     *
     * @throws LocalizedException
     */
    private function setCustomerGroup(CustomerInterface $customer)
    {
        $companyId = $this->customerSaveAttributes->getCompanyId();
        if ($companyId) {
            $company = $this->companyRepository->get($companyId);
            $customer->setGroupId($company->getCustomerGroupId());
        }
        return $customer;
    }

    /**
     * After customer save.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws CouldNotSaveException|LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer
    ) {
        $this->customerSaveAttributes->saveCompanyAttributes($customer);
        return $customer;
    }
}
