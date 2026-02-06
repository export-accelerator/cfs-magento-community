<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Test\Fixture;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\Data\CompanyCustomerInterface;
use Magento\Company\Api\Data\StructureInterface;
use Magento\Company\Model\Company\Structure;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\TestFramework\Fixture\DataFixtureInterface;

/**
 * Creating a new company
 */
class AssignCustomer implements DataFixtureInterface
{
    /**
     * @var CompanyManagementInterface
     */
    private CompanyManagementInterface $companyManagement;

    /**
     * @var Structure
     */
    private Structure $structureManager;

    /**
     * @param CompanyManagementInterface $companyManagement
     * @param Structure $structureManager
     */
    public function __construct(
        CompanyManagementInterface $companyManagement,
        Structure $structureManager
    ) {
        $this->companyManagement = $companyManagement;
        $this->structureManager = $structureManager;
    }

    /**
     * @inheritdoc
     */
    public function apply(array $data = []): ?DataObject
    {
        if (empty($data[CompanyCustomerInterface::COMPANY_ID])) {
            throw new InvalidArgumentException(
                __('"%field" is required', ['field' => CompanyCustomerInterface::COMPANY_ID])
            );
        }

        if (empty($data[CompanyCustomerInterface::CUSTOMER_ID])) {
            throw new InvalidArgumentException(
                __('"%field" is required', ['field' => CompanyCustomerInterface::CUSTOMER_ID])
            );
        }

        $this->companyManagement->assignCustomer(
            $data[CompanyCustomerInterface::COMPANY_ID],
            $data[CompanyCustomerInterface::CUSTOMER_ID]
        );

        $this->structureManager->addNode(
            $data[CompanyCustomerInterface::CUSTOMER_ID],
            StructureInterface::TYPE_CUSTOMER,
            $this->structureManager->getStructureByCustomerId(
                $this->companyManagement->getAdminByCompanyId($data[CompanyCustomerInterface::COMPANY_ID])->getId()
            )->getId()
        );
        return null;
    }
}
