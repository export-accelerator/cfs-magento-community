<?php

namespace Overdose\Brands\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;


abstract class Brands extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Overdose_Brands::od_brands';

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Edit Brands'), __('Edit Brands'))
            ->addBreadcrumb(__('Brands'), __('Brands'));

        $resultPage->getConfig()->getTitle()->prepend(__('Brands'));

        return $resultPage;
    }
}