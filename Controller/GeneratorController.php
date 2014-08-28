<?php

namespace Cekurte\GeneratorBundle\Controller;

use Cekurte\GeneratorBundle\Form\Type\FormCreateBundleType;
use Cekurte\GeneratorBundle\Form\Type\FormImportTableFromDatabaseType;
use Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\Request;

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

            $application    = $this->getApplication();

            $output         = new NullOutput();

            $input          = new ArrayInput(array(
                'command'           => 'doctrine:mapping:import',
                'bundle'            => $form->get('bundle')->getData(),
                'mapping-type'      => strtolower($form->get('mappingType')->getData()),
                '--filter'          => $this->getFilteredTableName($table),
                '--force'           => true,
            ));

            $resultCode = $application->run($input, $output);

            var_dump($resultCode);
            exit;

            $tableImported = $form->isValid();

            $this->get('session')->getFlashBag()->add('message', array(
                'type'      => $tableImported ? 'success' : 'error',
                'message'   => $tableImported
                    ? $this->get('translator')->trans('Table imported with successfully')
                    : $this->get('translator')->trans('The table was not imported'),
            ));
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
    public function createBundleAction(Request $request)
    {
        $form = $this->createForm(new FormCreateBundleType());

        if ($request->isMethod('POST')) {

            $form->bind($request);

            $application    = $this->getApplication();

            $output         = new NullOutput();

            $input          = new ArrayInput(array(
                'command'           => 'generate:bundle',
                '--namespace'       => $form->get('namespace')->getData(),
                '--format'          => strtolower($form->get('format')->getData()),
                '--structure'       => true,
                '--no-interaction'  => true,
            ));

            $resultCode = $application->run($input, $output);

            var_dump($resultCode);
            exit;

            $tableImported = $form->isValid();

            $this->get('session')->getFlashBag()->add('message', array(
                'type'      => $tableImported ? 'success' : 'error',
                'message'   => $tableImported
                        ? $this->get('translator')->trans('Table imported with successfully')
                        : $this->get('translator')->trans('The table was not imported'),
            ));
        }

        return array(
            'bundles'   => $this->getBundles(),
            'form'      => $form->createView(),
        );
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
     * Get a instance of Application
     *
     * @return Application
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getApplication()
    {
        $kernel = $this->get('kernel');

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->add(new ImportMappingDoctrineCommand());
        $application->add(new RouterDebugCommand());

        return $application;
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
