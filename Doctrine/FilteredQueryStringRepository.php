<?php

namespace Cekurte\GeneratorBundle\Doctrine;

use Cekurte\GeneratorBundle\Form\Type\DateType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Filtered QueryString Repository
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @see https://github.com/jpcercal/cekurtegeneratorbundle
 * @version 2.0
 */
class FilteredQueryStringRepository extends EntityRepository
{
    /**
     * @var string
     */
    const ENTITY_ALIAS = 'ck';

    /**
     * @var string
     */
    const QUERYSTRING_SEPARATOR_FIELD = '.';

    /**
     * @var string
     */
    const QUERYSTRING_SEPARATOR_OPERATION = ':';

    /**
     * @var string
     */
    const QUERYSTRING_SEPARATOR_EXPRESSION = ',';

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Get a filtered Query.
     *
     * @param array $queryString
     *
     * @return \Doctrine\ORM\Query
     */
    public function getFilteredQueryBuilder(array $queryString)
    {
        $this
            ->filterQueryStringSort($queryString)
            ->filterQueryStringFilters($queryString)
        ;

        return $this->getQueryBuilder();
    }

    /**
     * Get a QueryBuilder.
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        if (!$this->queryBuilder instanceof QueryBuilder) {
            $this->queryBuilder = $this->createQueryBuilder(self::ENTITY_ALIAS);
        }

        return $this->queryBuilder;
    }

    /**
     * Filter QueryString "Sort".
     *
     * @param array $queryString
     * @return EntityRepository
     */
    protected function filterQueryStringSort(array $queryString)
    {
        // ?sort=ck.id:asc,ck.title:desc
        if (!empty($queryString['sort'])) {

            $items = explode(self::QUERYSTRING_SEPARATOR_EXPRESSION, $queryString['sort']);

            foreach ($items as $item) {

                $data = explode(self::QUERYSTRING_SEPARATOR_OPERATION, $item);

                if (!in_array($data[0], $this->getQueryStringFieldsWhiteList())) {
                    continue;
                }

                $this->getQueryBuilder()->addOrderBy($data[0], $data[1]);
            }
        }

        return $this;
    }

    /**
     * Filter QueryString "Filters".
     *
     * @param array $queryString
     * @return EntityRepository
     */
    protected function filterQueryStringFilters(array $queryString)
    {
        // ?filters=ck.id:eq:1,ck.title:like:test
        if (!empty($queryString['filters'])) {

            $items = explode(self::QUERYSTRING_SEPARATOR_EXPRESSION, $queryString['filters']);

            foreach ($items as $item) {

                $data = explode(self::QUERYSTRING_SEPARATOR_OPERATION, $item);

                if (!in_array($data[0], $this->getQueryStringFieldsWhiteList())) {
                    continue;
                }

                $fieldParam = str_replace(self::QUERYSTRING_SEPARATOR_FIELD, '', $data[0]);
                $condition  = strtolower($data[1]);
                $value      = $condition === 'like' ? '%' . $data[2] . '%' : $data[2];

                $queryBuilder = $this->getQueryBuilder();

                $parameter = $queryBuilder->getParameter($fieldParam);

                // if parameter name exists on doctrine then the problem occur because doctrine
                // rewrite the existent parameter value...
                if ($parameter !== null) {
                    $fieldParam = $fieldParam . md5(microtime(true));
                }

                if (in_array($condition, array('gt', 'gte', 'lt', 'lte'))) {

                    $date = \Datetime::createFromFormat('U', DateType::getFormatter()->parse($value));

                    if ($date !== false) {
                        $value = $date->format('Y-m-d');
                    }
                }

                $queryBuilder
                    ->andWhere($queryBuilder->expr()->{$condition}($data[0], ':' . $fieldParam))
                    ->setParameter($fieldParam, $value)
                ;
            }
        }

        return $this;
    }
}
