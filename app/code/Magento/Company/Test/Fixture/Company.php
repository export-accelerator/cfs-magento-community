<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Company\Test\Fixture;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Framework\DataObject;
use Magento\TestFramework\Fixture\Api\DataMerger;
use Magento\TestFramework\Fixture\Api\ServiceFactory;
use Magento\TestFramework\Fixture\Data\ProcessorInterface;
use Magento\TestFramework\Fixture\RevertibleDataFixtureInterface;

/**
 * Creating a new company
 */
class Company implements RevertibleDataFixtureInterface
{
    private const DEFAULT_DATA_COMPANY = [
        'id' => null,
        CompanyInterface::NAME => 'Company #%uniqid%',
        CompanyInterface::LEGAL_NAME => null,
        CompanyInterface::COMPANY_EMAIL => 'company%uniqid%@magento.com',
        CompanyInterface::STATUS => CompanyInterface::STATUS_APPROVED,
        CompanyInterface::VAT_TAX_ID => null,
        CompanyInterface::RESELLER_ID => null,
        CompanyInterface::COMMENT => 'Comment',
        CompanyInterface::SUPER_USER_ID => null,
        CompanyInterface::SALES_REPRESENTATIVE_ID => null,
        CompanyInterface::CUSTOMER_GROUP_ID => 1,
        CompanyInterface::COUNTRY_ID => 'US',
        CompanyInterface::CITY => 'City',
        CompanyInterface::STREET => ['avenue, 30'],
        CompanyInterface::REGION => null,
        CompanyInterface::REGION_ID => 1,
        CompanyInterface::POSTCODE => '12354',
        CompanyInterface::TELEPHONE => '8001237654',
    ];

    /**
     * @var ServiceFactory
     */
    private ServiceFactory $serviceFactory;

    /**
     * @var DataMerger
     */
    private DataMerger $dataMerger;

    /**
     * @var ProcessorInterface
     */
    private ProcessorInterface $processor;

    /**
     * @param ServiceFactory $serviceFactory
     * @param DataMerger $dataMerger
     * @param ProcessorInterface $processor
     */
    public function __construct(
        ServiceFactory $serviceFactory,
        DataMerger $dataMerger,
        ProcessorInterface $processor
    ) {
        $this->serviceFactory = $serviceFactory;
        $this->dataMerger = $dataMerger;
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $data Parameters. Same format as Company::DEFAULT_DATA_COMPANY.
     * <pre>
     *  $data = [
     *    CompanyInterface::SUPER_USER_ID => (int) Company admin. Required.,
     *    CompanyInterface::SALES_REPRESENTATIVE_ID => (int) Company sales representative. Required.,
     *  ]
     * </pre>
     */
    public function apply(array $data = []): ?DataObject
    {
        return $this->serviceFactory->create(CompanyRepositoryInterface::class, 'save')->execute(
            [
                'company' => $this->processor->process(
                    $this,
                    $this->dataMerger->merge(self::DEFAULT_DATA_COMPANY, $data)
                )
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function revert(DataObject $data): void
    {
        $service = $this->serviceFactory->create(CompanyRepositoryInterface::class, 'deleteById');
        $data->setCompanyId($data->getId());
        $service->execute(
            [
                'companyId' => $data->getId()
            ]
        );
    }
}
