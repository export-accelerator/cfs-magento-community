<?php

namespace Overdose\Brands\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Page factory
     *
     * @var \Overdose\Brands\Model\ResourceModel\Product\Brands
     */
    protected $_brandsResource;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \\Overdose\Brands\Model\ResourceModel\Product\Brands $brandsResource
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Overdose\Brands\Model\ResourceModel\Product\Brands $brandsResource,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_brandsResource= $brandsResource;
        $this->_response = $response;
    }

    /**
     * Validate and Match Brands Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if(strpos('brands/', $identifier) !== false){
            return null;
        }

        $identifier = str_replace('brands/', '', $identifier);

        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $brandId = $this->_brandsResource->checkIdentifier($identifier);

        if (!$brandId) {
            return null;
        }

        $request->setModuleName('brands')->setControllerName('brand_info')->setActionName('view')->setParam('id', $brandId);
        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }
}
