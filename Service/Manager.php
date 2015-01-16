<?php

namespace Cekurte\GeneratorBundle\Service;

use Cekurte\ComponentBundle\Util\DoctrineContainerAware;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manager
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class Manager extends DoctrineContainerAware implements ManagerInterface
{
    /**
     * @var string
     */
    protected $resourceClassName;

    /**
     * @param string $resourceClassName
     */
    public function __construct($resourceClassName)
    {
        $this->setResourceClassName($resourceClassName);
    }

    /**
     * Set a resource class name.
     *
     * @param string $resourceClassName
     */
    protected function setResourceClassName($resourceClassName)
    {
        if (!is_string($resourceClassName)) {
            throw new \InvalidArgumentException('The resource class name could be a string.');
        }

        if (empty($resourceClassName)) {
            throw new \InvalidArgumentException('The resource class name could not be empty.');
        }

        $this->resourceClassName = $resourceClassName;
    }

    /**
     * @inheritdoc
     */
    public function getResourceClassName()
    {
        return $this->resourceClassName;
    }

    /**
     * @inheritdoc
     */
    public function findResourceAndThrowExceptionIfNotFound($parameters)
    {
        $resource = $this->getEntityRepository($this->getResourceClassName())->findOneBy($parameters);

        if (!$resource) {
            throw new NotFoundHttpException(sprintf(
                'The resource "%s" was not found. Filter conditions: "%s" with values "%s"',
                $this->getResourceClassName(),
                implode(', ', array_keys($parameters)),
                implode(', ', array_values($parameters))
            ));
        }

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getResourceLoggable($resource)
    {
        return $this->getEntityRepository('Gedmo\Loggable\Entity\LogEntry')->getLogEntries($resource);
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
        return $this->getEntityRepository($this->getResourceClassName())->findBy($parameters);
    }

    /**
     * @inheritdoc
     */
    public function findResourcesByQueryString($queryString)
    {
        return $this->getEntityRepository($this->getResourceClassName())->getFilteredQueryBuilder(
            $queryString
        );
    }

    /**
     * @inheritdoc
     */
    public function getResources(QueryBuilder $queryBuilder, $asArray = false)
    {
        $resources = $asArray === true
            ? $queryBuilder->getQuery()->getArrayResult()
            : $queryBuilder->getQuery()->getResult()
        ;

        return array(
            'total' => count($resources),
            'items' => $resources,
        );
    }

    /**
     * @inheritdoc
     */
    public function getPaginatedResources(QueryBuilder $queryBuilder, $page = 1, $limit = 10)
    {
        if ($page < 1) {
            throw new \InvalidArgumentException('The page parameter cannot be lower then 1.');
        }

        if ($limit < 1) {
            throw new \InvalidArgumentException('The limit parameter cannot be lower then 1.');
        }

        if ($limit > 500) {
            throw new \InvalidArgumentException('The limit parameter cannot be greater then 500.');
        }

        $paginator = new Paginator($queryBuilder);

        $paginator
            ->getQuery()
            ->setFirstResult($limit * (--$page))
            ->setMaxResults($limit)
        ;

        return array(
            'total' => count($paginator),
            'items' => $paginator->getIterator()->getArrayCopy(),
        );
    }

    /**
     * Save a resource.
     *
     * @param mixed $resource
     * @return mixed
     */
    protected function saveResource($resource)
    {
        $this->getManager()->persist($resource);
        $this->getManager()->flush();

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function createResource($resource)
    {
        return $this->saveResource($resource);
    }

    /**
     * @inheritdoc
     */
    public function updateResource($resource)
    {
        return $this->saveResource($resource);
    }

    /**
     * @inheritdoc
     */
    public function deleteResource($identifier)
    {
        $resource = $this->findResourceAndThrowExceptionIfNotFound(
            is_array($identifier) ? $identifier : array('id' => $identifier)
        );

        $this->getManager()->remove($resource);
        $this->getManager()->flush();

        return true;
    }
}
