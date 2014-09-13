<?php

namespace Cekurte\GeneratorBundle\Doctrine;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * EntityRepository
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class EntityRepository extends DoctrineEntityRepository
{
    /**
     * Get a filtered Query
     *
     * @param array $queryString
     *
     * @return \Doctrine\ORM\Query
     */
    public function getFilteredQuery(array $queryString)
    {
        $queryBuilder = $this->createQueryBuilder('ck');

        // ?count=ck.id:asc
        if (!is_null($queryString['count'])) {
            $queryBuilder->select('COUNT(ck.id) numberOfResources');
        } else {
            // ?fields=ck.id,ck.title
            if (!empty($queryString['fields'])) {
                $queryBuilder->select(implode(',', $queryString['fields']));
            }
        }

        // ?order=ck.id:asc,ck.title:desc
        if (!empty($queryString['order'])) {
            foreach ($queryString['order'] as $item) {
                $data = explode(':', $item);
                $queryBuilder->addOrderBy($data[0], $data[1]);
            }
        }

        // ?filters=ck.id:eq:1,ck.title:like:test
        if (!empty($queryString['filters'])) {
            foreach ($queryString['filters'] as $item) {
                $data = explode(':', $item);

                $field      = $data[0];
                $fieldParam = str_replace('.', '', $field);
                $condition  = strtolower($data[1]);
                $value      = $condition === 'like' ? '%' . $data[2] . '%' : $data[2];

                $queryBuilder
                    ->andWhere($queryBuilder->expr()->{$condition}($field, ':' . $fieldParam))
                    ->setParameter($fieldParam, $value)
                ;
            }
        }

        // ?joins=ck.categories:cat:inner,ck.categories:cat:left:eq:test
        if (!empty($queryString['joins'])) {
            foreach ($queryString['joins'] as $item) {
                $data = explode(':', $item);

                $suffix     = 'Join';
                $field      = $data[0];
                $alias      = $data[1];
                $joinType   = strtolower($data[2]) . $suffix;

                if (!isset($data[3]) and !isset($data[4])) {
                    $queryBuilder->{$joinType}($field, $alias);
                } else {

                    $fieldParam = str_replace('.', '', $field) . $suffix;
                    $condition  = strtolower($data[3]);
                    $value      = $condition === 'like' ? '%' . $data[4] . '%' : $data[4];

                    $queryBuilder
                        ->{$joinType}($field, $alias, Join::WITH, $queryBuilder->expr()->{$condition}($field, ':' . $fieldParam))
                        ->setParameter($fieldParam, $value)
                    ;
                }
            }
        }

        $queryBuilder->setMaxResults(3)->setFirstResult(6);

        return $queryBuilder;
    }
}
