<?php

namespace Cfs\Slider\Block\Adminhtml\Edit;

use Magento\Search\Controller\RegistryConstants;


class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    protected $request;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context Context
     * @param \Magento\Framework\App\Request\Http   $request Request
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $request;
    }

    /**
     * Return the synonyms group Id.
     *
     * @return int|null
     */
    public function getId()
    {
        $id = $this->request->getParam('id');
        if ($id)
            return $id;
        else
            return 0;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route  route
     *
     * @param array  $params params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
