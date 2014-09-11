<?php

namespace Cekurte\GeneratorBundle\Service;

use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     *
     * @return array
     */
    public function getResources(Request $request);

    /**
     * Find a resource given the parameters
     *
     * @api
     *
     * @param array $parameters
     *
     * @return array|\Doctrine\ORM\Query
     *
     * @throws NotFoundHttpException
     */
    public function findResourceAndThrowExceptionIfNotFound($parameters);

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
