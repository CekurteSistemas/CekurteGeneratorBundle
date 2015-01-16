<?php

namespace Cekurte\GeneratorBundle\Service;

use Doctrine\ORM\QueryBuilder;
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
     * Get the resource class name.
     *
     * @return string
     */
    public function getResourceClassName();

    /**
     * Get a resource loggable given the identifier or null.
     *
     * @api
     *
     * @param mixed $resource
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function getResourceLoggable($resource);

    /**
     * Get a resource given the identifier or null.
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
     * @param QueryBuilder $queryBuilder
     * @param bool $asArray
     *
     * @return array
     */
    public function getResources(QueryBuilder $queryBuilder, $asArray = false);

    /**
     * Get a list of resources (paginated).
     *
     * @param QueryBuilder $queryBuilder
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getPaginatedResources(QueryBuilder $queryBuilder, $page = 1, $limit = 10);

    /**
     * Find a resource given the parameters.
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
     * Find a resource given the parameters.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return array
     */
    public function findResources($parameters);

    /**
     * Find a resource given the querystring.
     *
     * @api
     *
     * @param array $queryString
     *
     * @return QueryBuilder
     */
    public function findResourcesByQueryString($queryString);

    /**
     * Create a new resource.
     *
     * @api
     *
     * @param mixed $resource
     *
     * @return mixed
     */
    public function createResource($resource);

    /**
     * Update a resource.
     *
     * @api
     *
     * @param mixed $resource
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function updateResource($resource);

    /**
     * Delete a resource.
     *
     * @api
     *
     * @param mixed $identifier
     *
     * @return bool
     *
     * @throws NotFoundHttpException
     */
    public function deleteResource($identifier);
}
