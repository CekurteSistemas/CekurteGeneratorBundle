<?php

namespace Cekurte\GeneratorBundle\Command;

use Cekurte\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Cekurte\GeneratorBundle\Generator\DoctrineFormGenerator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand as BaseCommand;

/**
 * Gera os CRUD's da Aplicacação
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class CrudCommand extends BaseCommand
{

    protected $generator;
    protected $formGenerator;

    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('cekurte:generate:crud');
        $this->setDescription('Gera os cruds da aplicação.');
    }

   protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        return array(
            __DIR__.'/../Resources/skeleton'
        );
    }

    protected function createGenerator($bundle = null)
    {
        return new DoctrineCrudGenerator($this->getContainer());
    }

    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new DoctrineFormGenerator($this->getContainer()->get('filesystem'));
            $this->formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->formGenerator;
    }
}
