<?php

namespace Greibit\Bundle\PanelAdminBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuExposeExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getGlobals()
    {
        return array(
            'greibit_panel_admin_menu_builder' => $this->container->getParameter('greibit_panel_admin.menu_builder'),
            'greibit_panel_admin_panel_title' => $this->container->getParameter('greibit_panel_admin.panel_title')
        );
    }

    public function getName()
    {
        return 'menu_expose_extension';
    }
}