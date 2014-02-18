<?php

/*
 * This file is part of the CrudGeneratorBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Greibit\Bundle\PanelAdminBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Greibit\Bundle\PanelAdminBundle\Generator\GreibitCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;


class GreibitCrudCommand extends GenerateDoctrineCrudCommand
{
    protected $generator;
    protected $formGenerator;

    protected function configure()
    {
        parent::configure();

        $this->setName('greibit:generate:crud');
        $this->setDescription('A CRUD generator with paginating and filters.');
    }

    protected function createGenerator($bundle = null)
    {
        return new GreibitCrudGenerator($this->getContainer()->get('filesystem'));
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@GreibitCrudGeneratorBundle/Resources/skeleton');
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@GreibitCrudGeneratorBundle/Resources');

        return $skeletonDirs;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'GreibitCrudGeneratorBundle');

        parent::interact($input, $output);
    }
}
