<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Test\Unit\Model\Customer;

use Laminas\Validator\EmailAddress;
use Magento\Company\Api\Data\CompanyInterfaceFactory;
use Magento\Company\Model\Customer\ValidateCustomerEmailAddress;
use Magento\Framework\App\Request\Http;
use Magento\Company\Model\ResourceModel\Company\CollectionFactory;
use Magento\Framework\Exception\InputException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Company\Api\Data\CompanyInterface;

/**
 * Unit test for ValidateCustomerEmailAddress.
 */
class ValidateCustomerEmailAddressTest extends TestCase
{
    /**
     * @var Http|MockObject
     */
    private $request;

    /**
     * @var EmailAddress|MockObject
     */
    private $emailValidator;

    /**
     * @var ValidateCustomerEmailAddress
     */
    private $validateCustomerEmailAddress;

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->request = $this->createMock(
            Http::class
        );
        $this->emailValidator = $this->createMock(
            EmailAddress::class
        );
        $this->validateCustomerEmailAddress = new ValidateCustomerEmailAddress(
            $this->request,
            $this->emailValidator
        );
    }

    /**
     * Test invalid email address when creating a company account with exception
     *
     * @return void
     * @throws InputException
     */
    public function testExecuteWithException()
    {
        $this->expectException('Magento\Framework\Exception\InputException');
        $companyData = [
            'general' =>
                [
                    CompanyInterface::COMPANY_ID => 1,
                    CompanyInterface::STATUS => 'enabled',
                    CompanyInterface::NAME => 'Test Corp 1',
                    CompanyInterface::COMPANY_EMAIL => 'test@email.cooom'
                ],
            'customer' =>
                [
                    CompanyInterface::EMAIL => 'customer@email.com',
                    CompanyInterface::GENDER => 'male'
                ]
        ];
        $this->expectExceptionMessage(
            'Invalid value of "'.$companyData['general'][CompanyInterface::COMPANY_EMAIL].
            '" provided for the company_email field.',
        );
        $this->request->expects($this->any())->method('getParams')->willReturn($companyData);
        $this->emailValidator->expects($this->any())
            ->method('isValid')
            ->with($companyData['general'][CompanyInterface::COMPANY_EMAIL])
            ->willReturn(false);
        $this->validateCustomerEmailAddress->execute();
    }

    /**
     * Test valid email address when creating a company account
     *
     * @return void
     * @throws InputException
     */
    public function testExecuteWithoutException()
    {
        $companyData = [
            'general' =>
                [
                    CompanyInterface::COMPANY_ID => 2,
                    CompanyInterface::STATUS => 'enabled',
                    CompanyInterface::NAME => 'Test Corp 2',
                    CompanyInterface::COMPANY_EMAIL => 'test@email.cooom'
                ],
            'customer' =>
                [
                    CompanyInterface::EMAIL => 'customer@email.com',
                    CompanyInterface::GENDER => 'male'
                ]
        ];
        $this->request->expects($this->any())->method('getParams')->willReturn($companyData);
        $this->emailValidator->expects($this->any())
            ->method('isValid')
            ->with($companyData['general'][CompanyInterface::COMPANY_EMAIL])
            ->willReturn(true);
        $this->validateCustomerEmailAddress->execute();
    }
}
