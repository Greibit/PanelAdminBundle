<?php

namespace Greibit\Bundle\PanelAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('greibit_panel_admin');

        $rootNode
            ->children()
                ->scalarNode('menu_builder')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('panel_title')->defaultValue("Greibit Administration")->end()
            ->end();

        return $treeBuilder;
    }
}
