<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cfs\Topmenu\Block\Html;

use Magento\Backend\Model\Menu;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Html page top menu block
 *
 * @api
 * @since 100.0.2
 */
class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    
    /**
     * Cache identities
     *
     * @var array
     */
    protected $identities = [];

    /**
     * Top menu data tree
     *
     * @var Node
     */
    protected $_menu;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

    protected $helper;
    /**
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        \Cfs\Topmenu\Helper\Data $helper,
        array $data = []
    ) {
        
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->helper = $helper;
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     */
    protected function _getHtml(
        Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $childLevel = $this->getChildLevel($menuTree->getLevel());
        $this->removeChildrenWithoutActiveParent($children, $childLevel);

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var Node $child */
        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter === 1);
            $child->setIsLast($counter === $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel === 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $this->setCurrentClass($child, $outermostClass);
            }

            if ($this->shouldAddNewColumn($colBrakes, $counter)) {
                $html .= '</ul></li><li class="column"><ul>';
            }
            
            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';

            if($childLevel >= 1 ){
                $iconUrl = $this->helper->getIconByNodeId($child->getId());
                if(!empty($iconUrl)){
                    $iconUrl = $this->getUrl().trim($iconUrl,'/');
                    $html .= '<span class="icon-wrapper"><img src="'.$iconUrl.'"></span>';
                }
            }
    
            $name = $child->getName();

            if ($childLevel === 0) {
                $title = (string) $this->helper->getTopCategoryTitle();
                $title = trim($title);
                if(!empty($title)){
                    $name = $title;
                }
            }

            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                $name
            ) . '</span></a>' . $this->_addSubMenu(
                $child,
                $childLevel,
                $childrenWrapClass,
                $limit
            ) . '</li>';
            $counter++;
        }

        if (is_array($colBrakes) && !empty($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

 
    private function removeChildrenWithoutActiveParent(Collection $children, int $childLevel): void
    {
        /** @var Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                $children->delete($child);
            }
        }
    }

    /**
     * Retrieve child level based on parent level
     *
     * @param int $parentLevel
     *
     * @return int
     */
    private function getChildLevel($parentLevel): int
    {
        return $parentLevel === null ? 0 : $parentLevel + 1;
    }

    /**
     * Check if new column should be added.
     *
     * @param array $colBrakes
     * @param int $counter
     * @return bool
     */
    private function shouldAddNewColumn(array $colBrakes, int $counter): bool
    {
        return count($colBrakes) && $colBrakes[$counter]['colbrake'];
    }

    /**
     * Set current class.
     *
     * @param Node $child
     * @param string $outermostClass
     */
    private function setCurrentClass(Node $child, string $outermostClass): void
    {
        $currentClass = $child->getClass();
        if (empty($currentClass)) {
            $child->setClass($outermostClass);
        } else {
            $child->setClass($currentClass . ' ' . $outermostClass);
        }
    }
}
