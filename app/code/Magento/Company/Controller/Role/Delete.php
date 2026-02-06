<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Company\Controller\Role;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Controller for role deleting.
 */
class Delete extends \Magento\Company\Controller\AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a company session.
     */
    public const COMPANY_RESOURCE = 'Magento_Company::roles_edit';

    /**
     * @var \Magento\Company\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Magento\Company\Model\CompanyUser
     */
    private $companyUser;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Company\Model\CompanyContext $companyContext
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Company\Api\RoleRepositoryInterface $roleRepository
     * @param \Magento\Company\Model\CompanyUser $companyUser
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Company\Model\CompanyContext $companyContext,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Company\Api\RoleRepositoryInterface $roleRepository,
        \Magento\Company\Model\CompanyUser $companyUser
    ) {
        parent::__construct($context, $companyContext, $logger);
        $this->roleRepository = $roleRepository;
        $this->companyUser = $companyUser;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $roleId = $request->getParam('id');
        try {
            $role = $this->roleRepository->get($roleId);
            $companyId = $this->companyUser->getCurrentCompanyId();

            if ($role->getCompanyId() != $companyId) {
                return $this->jsonError(__('Bad Request'));
            }

            $this->roleRepository->delete($role->getId());

            return $this->handleJsonSuccess(
                __(
                    'You have deleted role %companyRoleName.',
                    ['companyRoleName' => $role ? $role->getRoleName() : '']
                )
            );
        } catch (LocalizedException $e) {
            return $this->handleJsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->handleJsonError(__('Something went wrong. Please try again later.'));
        }
    }
}
