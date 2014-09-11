<?php

namespace Cekurte\GeneratorBundle\Service;

use Cekurte\ComponentBundle\Util\RequestContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manager
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class Manager extends RequestContainerAware implements ManagerInterface
{
    /**
     * Get the resource class name
     *
     * @return string
     */
    abstract protected function getResourceClass();

    /**
     * Find a resource(s) given the parameters
     *
     * @api
     *
     * @param array $parameters
     * @param bool $findOneResource
     *
     * @return array|\Doctrine\ORM\Query
     *
     * @throws NotFoundHttpException
     */
    protected function findResourcesAndThrowExceptionIfNotFound($parameters, $findOneResource)
    {
        $queryBuilder = $this->getRepository($this->getResourceClass())->createQueryBuilder('ck');

        if ($findOneResource === true) {

            $resource = $this->getRepository($this->getResourceClass())->findOneBy($parameters);

            if (!$resource) {
                throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', implode(', ', array_keys($parameters))));
            }

            return $resource;
        }

        foreach ($parameters as $identifier => $value) {

            $column = strpos($identifier, '.') > 0
                ? $identifier
                : 'ck.' . $identifier
            ;

            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq($column, sprintf(':%s', $identifier)))
                ->setParameter($identifier, $value)
            ;
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @inheritdoc
     */
    public function getResource($identifier, $field = 'id')
    {
        return $this->findResource(array(
            $field => $identifier
        ));
    }

    /**
     * @inheritdoc
     */
    public function getResources($format, $page = 1, $numberResourcesPerPage = 10)
    {
        $resources = $this->findResources(array('title' => 'title'));

        $pagination = $this->getContainer()->get('knp_paginator')->paginate($resources, $page, $numberResourcesPerPage);

        return !in_array(strtolower($format), array('json', 'xml'))
            ? $pagination
            : array(
                'total' => $pagination->getTotalItemCount(),
                'itens' => $pagination->getItems(),
            )
        ;
    }

    /**
     * @inheritdoc
     */
    public function findResource($parameters)
    {
        return $this->findResourcesAndThrowExceptionIfNotFound($parameters, true);
    }

    /**
     * @inheritdoc
     */
    public function findResources($parameters)
    {
        return $this->findResourcesAndThrowExceptionIfNotFound($parameters, false);
    }
}
