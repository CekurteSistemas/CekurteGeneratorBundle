<?php

namespace Cekurte\GeneratorBundle\Controller;

use Cekurte\GeneratorBundle\Form\Type\FormCreateBundleType;
use Cekurte\GeneratorBundle\Form\Type\FormImportTableFromDatabaseType;
use Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand;
use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

/**
 * Generator Controller.
 *
 * @Route("/admin/generator")
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class GeneratorController extends CekurteController
{
    /**
     * Lists all entities.
     *
     * @Route("/", name="cekurte_generator")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function indexAction()
    {
        return array(
            'entities'  => $this->getOrdenedAllMetadata(),
            'tables'    => $this->getTableNames(),
        );
    }

    /**
     * Finds and displays a setup to generate crud.
     *
     * @Route("/bundle/{bundle}/entity/{entity}/", name="cekurte_generator_bundle_entity")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @param string $bundle
     * @param string $entity
     *
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showBundleEntityAction($bundle, $entity)
    {
        return array(
            'bundle'    => $bundle,
            'entity'    => $entity,
            'fields'    => $this->getMetadataFieldMappings($bundle, $entity)
        );
    }

    /**
     * Generate the methods setters and getters to entity.
     *
     * @Route("/bundle/{bundle}/entity/{entity}/generate/entities", name="cekurte_generator_bundle_generate_entities")
     * @Method("POST")
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @param string $bundle
     * @param string $entity
     *
     * @return RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function generateEntitiesAction($bundle, $entity)
    {
        $input = new ArrayInput($params = array(
            'command'           => 'doctrine:generate:entities',
            'name'              => $bundle . ':' . $entity,
            '--no-interaction',
        ));

        $process = $this->getProcess($input);

        $process->run();

        while ($process->isRunning()) {
            // ...
        }

        $flashBag = $this->get('session')->getFlashBag();

        if ($process->isSuccessful()) {

            $flashBag->add('commandOutput', array(
                'type'      => 'success',
                'message'   => $process->getOutput(),
            ));

            $flashBag->add('message', array(
                'type'      => 'success',
                'message'   => $this->get('translator')->trans('Entities generated with successfully'),
            ));

        } else {

            $flashBag->add('commandOutput', array(
                'type'      => 'error',
                'message'   => $process->getErrorOutput(),
            ));

            $flashBag->add('message', array(
                'type'      => 'error',
                'message'   => $this->get('translator')->trans('The entities was not generated'),
            ));
        }

        return $this->redirect($this->generateUrl('cekurte_generator_bundle_entity', array(
            'bundle'    => $bundle,
            'entity'    => $entity,
        )));
    }

    /**
     * Finds and displays a columns from table.
     *
     * @Route("/table/{table}/", name="cekurte_generator_table")
     * @Method({"GET", "POST"})
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @param Request $request
     * @param string $table
     *
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showTableAction(Request $request, $table)
    {
        $form = $this->createForm(new FormImportTableFromDatabaseType(), null, array(
            'registeredBundles' => $this->getBundles()
        ));

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $input = new ArrayInput($params = array(
                    'command'           => 'doctrine:mapping:import',
                    'bundle'            => $form->get('bundle')->getData(),
                    'mapping-type'      => strtolower($form->get('mappingType')->getData()),
                    '--filter'          => $this->getFilteredTableName($table),
                    '--force',
                ));

                $process = $this->getProcess($input);

                $process->run();

                while ($process->isRunning()) {
                    // ...
                }

                $flashBag = $this->get('session')->getFlashBag();

                if ($process->isSuccessful()) {

                    $flashBag->add('commandOutput', array(
                        'type'      => 'success',
                        'message'   => $process->getOutput(),
                    ));

                    $flashBag->add('message', array(
                        'type'      => 'success',
                        'message'   => $this->get('translator')->trans('Table imported with successfully'),
                    ));

                } else {

                    $flashBag->add('commandOutput', array(
                        'type'      => 'error',
                        'message'   => $process->getErrorOutput(),
                    ));

                    $flashBag->add('message', array(
                        'type'      => 'error',
                        'message'   => $this->get('translator')->trans('The table was not imported'),
                    ));
                }
            }
        }

        return array(
            'table'     => $table,
            'fields'    => $this->getColumnsFromTableName($table),
            'form'      => $form->createView(),
        );
    }

    /**
     * Finds and displays a columns from table.
     *
     * @Route("/bundle", name="cekurte_generator_bundle")
     * @Method({"GET", "POST"})
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @param Request $request
     *
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function bundleAction(Request $request)
    {
        $form = $this->createForm(new FormCreateBundleType());

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $redirect = $this->redirect($this->generateUrl('cekurte_generator_bundle'));

                $input = new ArrayInput($params = array(
                    'command'           => 'generate:bundle',
                    '--namespace'       => str_replace('/', '\\', $form->get('namespace')->getData()),
                    '--format'          => strtolower($form->get('format')->getData()),
                    '--dir'             => $this->getSrcDirectory(),
                    '--no-interaction',
                ));

                $process = $this->getProcess($input);

                $process->run();

                while ($process->isRunning()) {
                    // ...
                }

                $flashBag = $this->get('session')->getFlashBag();

                if ($process->isSuccessful()) {

                    $flashBag->add('commandOutput', array(
                        'type'      => 'success',
                        'message'   => $process->getOutput(),
                    ));

                    $flashBag->add('message', array(
                        'type'      => 'success',
                        'message'   => $this->get('translator')->trans('Bundle generated with successfully'),
                    ));

                    sleep(5);

                    return $redirect;
                }

                $flashBag->add('commandOutput', array(
                    'type'      => 'error',
                    'message'   => $process->getErrorOutput(),
                ));

                $flashBag->add('message', array(
                    'type'      => 'error',
                    'message'   => $this->get('translator')->trans('The bundle was not generated'),
                ));
            }
        }

        return array(
            'bundles'   => $this->getBundles(),
            'form'      => $form->createView(),
        );
    }

    /**
     * Get the process instance to execute a command
     *
     * @param ArrayInput $input
     *
     * @return Process
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getProcess(ArrayInput $input)
    {
        $process = new Process(sprintf('%s %s %s', 'php', 'app/console', (string) $input));

        $process->setWorkingDirectory($this->getProjectDirectory());

        return $process;
    }

    /**
     * Get the project directory
     *
     * @return string
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getProjectDirectory()
    {
        return realpath($this->getAppDirectory() . '/../');
    }

    /**
     * Get the app directory
     *
     * @return string
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getAppDirectory()
    {
        return $this->get('kernel')->getRootDir();
    }

    /**
     * Get the src directory
     *
     * @return string
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getSrcDirectory()
    {
        return realpath($this->getProjectDirectory() . '/src');
    }

    /**
     * Get the registered bundles
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getBundles()
    {
        $bundles = array_keys($this->get('kernel')->getBundles());

        sort($bundles);

        $data = array();

        foreach ($bundles as $item) {
            $data[$item] = $item;
        }

        return $data;
    }

    /**
     * Get a filtered table name
     *
     * @param string $table
     *
     * @return string
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getFilteredTableName($table)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($table))));
    }

    /**
     * Get the table names.
     *
     * @param string|null $connection
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getTableNames($connection = null)
    {
        return $this->getDoctrine()->getConnection($connection)->getSchemaManager()->listTableNames();
    }

    /**
     * Get the columns definitions from table name.
     *
     * @param string $table
     * @param string|null $connection
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getColumnsFromTableName($table, $connection = null)
    {
        return $this->getDoctrine()->getConnection($connection)->getSchemaManager()->listTableColumns($table);
    }

    /**
     * Get metadata field mappings to one entity bundle.
     *
     * @param string $bundle
     * @param string $entity
     * @param string|null $connection
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getMetadataFieldMappings($bundle, $entity, $connection = null)
    {
        $metadata = $this
            ->getDoctrine()
            ->getManager($connection)
            ->getMetadataFactory()
            ->getMetadataFor(sprintf('%s:%s', $bundle, $entity))
        ;

        return $metadata->fieldMappings;
    }

    /**
     * Get all metadata from database with results ordened by string
     *
     * @param string|null $connection
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getOrdenedAllMetadata($connection = null)
    {
        $metadata = $this->getAllMetadata($connection);

        sort($metadata);

        return $metadata;
    }

    /**
     * Get all metadata from database
     *
     * @param string|null $connection
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getAllMetadata($connection = null)
    {
        $metadata = $this->getDoctrine()->getManager($connection)->getMetadataFactory()->getAllMetadata();

        $patternBundle = "/(.*)Bundle/";
        $patternEntity = "/Entity(.*)/";

        $data = array();

        foreach ($metadata as $item) {

            if ($item->isMappedSuperclass === false) {

                $matchesBundle = array();
                $matchesEntity = array();

                preg_match($patternBundle, $item->namespace, $matchesBundle);
                preg_match($patternEntity, $item->name, $matchesEntity);

                $data[] = array(
                    'bundle'    => str_replace('\\', '', $matchesBundle[0]),
                    'entity'    => str_replace('\\', '', $matchesEntity[1]),
                );
            }
        }

        return $data;
    }
}
