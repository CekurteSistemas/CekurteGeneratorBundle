<?php

namespace Cekurte\GeneratorBundle\Service;

use Cekurte\ComponentBundle\Util\DoctrineContainerAware;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manager
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class Manager extends DoctrineContainerAware implements ManagerInterface
{
    /**
     * Get the resource class name
     *
     * @return string
     */
    abstract protected function getResourceClass();

    /**
     * @inheritdoc
     */
    public function findResourceAndThrowExceptionIfNotFound($parameters)
    {
        $resource = $this->getRepository($this->getResourceClass())->findOneBy($parameters);

        if (!$resource) {
            throw new NotFoundHttpException(sprintf(
                'The resource "%s" was not found. Filter conditions: "%s" with values "%s"',
                $this->getResourceClass(),
                implode(', ', array_keys($parameters)),
                implode(', ', array_values($parameters))
            ));
        }

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getResource($identifier, $field = 'id')
    {
        return $this->findResourceAndThrowExceptionIfNotFound(array(
            $field => $identifier
        ));
    }

    /**
     * @inheritdoc
     */
    public function findResources($parameters)
    {
        return $this->getRepository($this->getResourceClass())->getFilteredQuery(
            $parameters
        );
    }

    /**
     * @inheritdoc
     */
    public function getResources(Request $request)
    {
        $fields     = $request->get('fields',  null);
        $joins      = $request->get('joins',   null);
        $filters    = $request->get('filters', null);
        $order      = $request->get('order',   null);

        $queryString = array(
            'format'    => $request->get('_format', 'html'),
            'offset'    => $request->get('offset', 0),
            'limit'     => $request->get('limit', 10),

            'count'     => $request->get('count', null),

            'fields'    => empty($fields)   ? array() : explode(',', $fields),
            'joins'     => empty($joins)    ? array() : explode(',', $joins),
            'filters'   => empty($filters)  ? array() : explode(',', $filters),
            'order'     => empty($order)    ? array() : explode(',', $order),
        );

        $queryBuilder = $this->findResources($queryString);


        $adapter = new \Pagerfanta\Adapter\DoctrineORMAdapter($queryBuilder);

        $pagination = new Pagerfanta($adapter);

        var_dump(
            $pagination->getNbResults(),
            $pagination->getCurrentPageResults(),
            $pagination->getNbPages()
        );
        exit;

        $pagination = $this->getContainer()->get('knp_paginator')->paginate($resources, ++$queryString['offset'], $queryString['limit']);

        return !in_array(strtolower($queryString['format']), array('json', 'xml'))
            ? $pagination
            : array(
                'total' => $pagination->getTotalItemCount(),
                'itens' => $pagination->getItems(),
            )
        ;
    }
}
