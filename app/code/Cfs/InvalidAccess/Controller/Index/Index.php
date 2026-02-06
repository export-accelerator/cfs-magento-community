<?php

namespace Cfs\InvalidAccess\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $orderFactory;
    protected $logger;

     /**
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Psr\Log\LoggerInterface $logger
     */

    protected $_resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
       
    ) {
        $this->_resultPageFactory = $resultPageFactory;
       
        parent::__construct($context);
    }
    
    public function execute()
    {
        
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
