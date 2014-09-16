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

        // ?sort=ck.id:asc,ck.title:desc
        if (!empty($queryString['sort'])) {
            foreach ($queryString['sort'] as $item) {
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

        return $queryBuilder;
    }
}
