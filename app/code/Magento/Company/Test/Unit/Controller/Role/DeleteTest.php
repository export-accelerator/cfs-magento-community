<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Test\Unit\Controller\Role;

use Magento\Company\Api\Data\RoleInterface;
use Magento\Company\Controller\Role\Delete;
use Magento\Company\Model\CompanyUser;
use Magento\Company\Model\RoleRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test for Delete.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeleteTest extends TestCase
{
    /**
     * @var RoleRepository|MockObject
     */
    private $roleRepository;

    /**
     * @var CompanyUser|MockObject
     */
    private $companyUser;

    /**
     * @var RequestInterface|MockObject
     */
    private $request;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactory;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManager;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var Json|MockObject
     */
    private $resultJson;

    /**
     * @var Delete
     */
    private $delete;

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->roleRepository = $this->getMockBuilder(RoleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->companyUser = $this->getMockBuilder(CompanyUser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->resultRedirectFactory = $this
            ->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();
        $this->messageManager = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $resultFactory = $this->createPartialMock(ResultFactory::class, ['create']);
        $this->resultJson = $this->createPartialMock(Json::class, ['setData']);
        $resultFactory->expects($this->once())->method('create')->willReturn($this->resultJson);

        $objectManagerHelper = new ObjectManager($this);
        $this->delete = $objectManagerHelper->getObject(
            Delete::class,
            [
                'resultFactory' => $resultFactory,
                'roleRepository' => $this->roleRepository,
                'companyUser' => $this->companyUser,
                '_request' => $this->request,
                'resultRedirectFactory' => $this->resultRedirectFactory,
                'messageManager' => $this->messageManager,
                'logger' => $this->logger,
            ]
        );
    }

    /**
     * Test for execute method.
     *
     * @return void
     */
    public function testExecute()
    {
        $roleId = 1;
        $roleName = 'Role 1';
        $companyId = 2;
        $this->request->expects($this->once())->method('getParam')->with('id')->willReturn($roleId);
        $role = $this->getMockBuilder(RoleInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $role->expects($this->once())->method('getId')->willReturn($roleId);
        $role->expects($this->once())->method('getCompanyId')->willReturn($companyId);
        $role->expects($this->once())->method('getRoleName')->willReturn($roleName);
        $this->roleRepository->expects($this->once())->method('get')->with($roleId)->willReturn($role);
        $this->companyUser->expects($this->once())->method('getCurrentCompanyId')->willReturn($companyId);
        $this->roleRepository->expects($this->once())->method('delete')->with($roleId)->willReturn(true);
        $result = '';
        $setDataCallback = function ($data) use (&$result) {
            $result = $data['status'];
        };
        $this->resultJson->expects($this->once())->method('setData')->willReturnCallback($setDataCallback);
        $this->delete->execute();
        $this->assertEquals($result, "ok");
    }

    /**
     * Test for execute method with bad request exception.
     *
     * @return void
     */
    public function testExecuteWithBadRequestException()
    {
        $roleId = 1;
        $companyId = 2;
        $this->request->expects($this->once())->method('getParam')->with('id')->willReturn($roleId);
        $role = $this->getMockBuilder(RoleInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $role->expects($this->once())->method('getCompanyId')->willReturn($companyId);
        $this->roleRepository->expects($this->once())->method('get')->with($roleId)->willReturn($role);
        $this->companyUser->expects($this->once())->method('getCurrentCompanyId')->willReturn(null);
        $result = '';
        $setDataCallback = function ($data) use (&$result) {
            $result = $data['status'];
        };
        $this->resultJson->expects($this->once())->method('setData')->willReturnCallback($setDataCallback);
        $this->delete->execute();
        $this->assertEquals($result, "error");
    }

    /**
     * Test for execute method with NoSuchEntityException.
     *
     * @return void
     */
    public function testExecuteWithNoSuchEntityException()
    {
        $roleId = 1;
        $this->request->expects($this->once())->method('getParam')->with('id')->willReturn($roleId);
        $this->roleRepository->expects($this->once())->method('get')->with($roleId)->willThrowException(
            new NoSuchEntityException()
        );
        $result = '';
        $setDataCallback = function ($data) use (&$result) {
            $result = $data['status'];
        };
        $this->resultJson->expects($this->once())->method('setData')->willReturnCallback($setDataCallback);
        $this->delete->execute();
        $this->assertEquals($result, "error");
    }

    /**
     * Test for execute method with \Exception.
     *
     * @return void
     */
    public function testExecuteWithException()
    {
        $roleId = 1;
        $exception = new \Exception();
        $this->request->expects($this->once())->method('getParam')->with('id')->willReturn($roleId);
        $this->roleRepository->expects($this->once())->method('get')->with($roleId)->willThrowException($exception);
        $this->logger->expects($this->once())->method('critical')->with($exception);
        $result = '';
        $setDataCallback = function ($data) use (&$result) {
            $result = $data['status'];
        };
        $this->resultJson->expects($this->once())->method('setData')->willReturnCallback($setDataCallback);
        $this->delete->execute();
        $this->assertEquals($result, "error");
    }
}
