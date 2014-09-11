<?php

namespace Cekurte\GeneratorBundle\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ManagerInterface
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
interface ManagerInterface 
{
    /**
     * Get a resource given the identifier
     *
     * @api
     *
     * @param mixed $identifier
     * @param string $field
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function getResource($identifier, $field = 'id');

    /**
     * Get a list of resources.
     *
     * @param string $format the format of content (html, json, xml)
     * @param int $page page number, starting from 1
     * @param int $numberResourcesPerPage number of items per page
     *
     * @return array
     */
    public function getResources($format, $page = 1, $numberResourcesPerPage = 10);

    /**
     * Find a resource given the parameters
     *
     * @api
     *
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function findResource($parameters);

    /**
     * Find a resource given the parameters
     *
     * @api
     *
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function findResources($parameters);
}
