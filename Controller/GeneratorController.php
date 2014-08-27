<?php

namespace Cekurte\GeneratorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
            'entities' => $this->getOrdenedAllMetadata()
        );
    }

    /**
     * Finds and displays a setup to generate crud.
     *
     * @Route("/bundle/{bundle}/entity/{entity}/", name="cekurte_generator_show")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEADMINTESTBUNDLE_TEST_RETRIEVE, ROLE_ADMIN")
     *
     * @param string $bundle
     * @param string $entity
     *
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showAction($bundle, $entity)
    {
        return array(
            'bundle'    => $bundle,
            'entity'    => $entity,
            'fields'    => $this->getMetadataFieldMappings($bundle, $entity)
        );
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
    public function getMetadataFieldMappings($bundle, $entity, $connection = null)
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
