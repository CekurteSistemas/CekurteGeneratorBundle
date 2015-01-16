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
        $this->setDescription("Generate the custom CRUD's.");
        $this->setHelp(<<<EOT
The <info>cekurte:generate:crud</info> command generates a CRUD based on a Doctrine entity.

<info>php app/console cekurte:generate:crud --with-write --overwrite --format=annotation --no-interaction --entity=AcmeBlogBundle:Post --route-prefix=admin_post</info>

The command above generate all controller actions to entity <info>Post</info> on <info>AcmeBlogBundle</info> and the prefix <info>admin_post</info> generate the route <info>/admin/post/*</info>.
EOT
    );
    }

    // todo: Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineEntityCommand (interact method)

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
