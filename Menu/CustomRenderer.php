<?php

namespace Greibit\Bundle\PanelAdminBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\RendererInterface;

class CustomRenderer extends ListRenderer implements RendererInterface
{
    protected function renderList(ItemInterface $item, array $attributes, array $options)
    {
        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
            return '';
        }

        if($item->getLevel() == 0){
            $html = $this->format('<ul class="nav" id="side-menu">', 'ul', $item->getLevel(), $options);
        }else{
            $html = $this->format('<ul class="nav nav-second-level">', 'ul', $item->getLevel(), $options);
        }

        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</ul>', 'ul', $item->getLevel(), $options);

        return $html;
    }

    protected function renderLinkElement(ItemInterface $item, array $options)
    {
        $html = sprintf('<a href="%s"%s>', $this->escape($item->getUri()), $this->renderHtmlAttributes($item->getLinkAttributes()));

        if($icon = $item->getAttribute("icon")){
            $html .= '<i class="fa fa-'.$icon.' fa-fw"></i> ';
        }

        $html .= $this->renderLabel($item, $options);

        if($item->hasChildren()){
            $html .= '<span class="fa arrow"></span>';
        }

        $html .= '</a>';

        return $html;
    }

    protected function renderSpanElement(ItemInterface $item, array $options)
    {
        $html = sprintf('<span%s>', $this->renderHtmlAttributes($item->getLinkAttributes()));

        if($icon = $item->getAttribute("icon")){
            $html .= '<i class="fa fa-'.$icon.' fa-fw"></i> ';
        }

        $html .= $this->renderLabel($item, $options);

        if($item->hasChildren()){
            $html .= '<span class="fa arrow"></span>';
        }

        $html .= '</span>';

        return $html;
    }
}