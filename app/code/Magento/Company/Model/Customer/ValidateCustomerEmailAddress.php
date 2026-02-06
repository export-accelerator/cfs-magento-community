<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Model\Customer;

use Laminas\Validator\EmailAddress;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\InputException;
use Magento\Company\Api\Data\CompanyInterface;

/**
 * Validate customer email address before creating customer account
 */
class ValidateCustomerEmailAddress
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @var EmailAddress
     */
    private $emailValidator;

    /**
     * ValidateCustomerEmailAddress constructor
     *
     * @param Http $request
     * @param EmailAddress $emailValidator
     */
    public function __construct(
        Http $request,
        EmailAddress $emailValidator
    ) {
        $this->request = $request;
        $this->emailValidator = $emailValidator;
    }

    /**
     * Check company email validation before creating company customer user account
     *
     * @return void
     * @throws InputException
     */
    public function execute(): void
    {
        $companyData = $this->extractCompanyData();
        if (!empty($companyData[CompanyInterface::COMPANY_EMAIL])) {
            $isEmailAddress = $this->emailValidator->isValid($companyData[CompanyInterface::COMPANY_EMAIL]);

            if (!$isEmailAddress) {
                throw new InputException(
                    __(
                        'Invalid value of "%value" provided for the %fieldName field.',
                        ['fieldName' => 'company_email', 'value' => $companyData[CompanyInterface::COMPANY_EMAIL]]
                    )
                );
            }
        }
    }

    /**
     * Filter request to get just list of fields.
     *
     * @return array
     */
    private function extractCompanyData(): array
    {
        $result = [];
        $allFormFields = [
            CompanyInterface::COMPANY_ID,
            CompanyInterface::STATUS,
            CompanyInterface::NAME,
            CompanyInterface::LEGAL_NAME,
            CompanyInterface::COMPANY_EMAIL,
            CompanyInterface::EMAIL,
            CompanyInterface::VAT_TAX_ID,
            CompanyInterface::RESELLER_ID,
            CompanyInterface::GENDER
        ];
        $request = $this->request->getParams();
        unset($request['use_default']);
        if (is_array($request)) {
            foreach ($request as $fields) {
                if (!is_array($fields)) {
                    continue;
                }
                $result = array_merge_recursive($result, $fields);
            }
        }
        return array_intersect_key($result, array_flip($allFormFields));
    }
}
