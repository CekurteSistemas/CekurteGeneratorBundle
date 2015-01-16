<?php

namespace Cekurte\GeneratorBundle\Twig\Extension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Pagination Extension
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class PaginationExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->data         = array();
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return RequestStack
     */
    public function getRequestStack()
    {
        return $this->requestStack;
    }

    /**
     * @inherited
     */
    public function getFunctions()
    {
        return array(
            'cekurte_pagination_sorted_by'      => new \Twig_Function_Method($this, 'checkWhetherResultsAreSortedByColumn'),
            'cekurte_pagination_sort_direction' => new \Twig_Function_Method($this, 'getColumnSortedDirection'),
            'cekurte_pagination_render'         => new \Twig_Function_Method($this, 'renderPaginationTemplate'),
            'cekurte_pagination_sortable'       => new \Twig_Function_Method($this, 'renderPaginationSortable'),
            'cekurte_pagination_query_string'   => new \Twig_Function_Method($this, 'buildPaginationRouteQueryString'),
        );
    }

    /**
     * Get query string from request
     *
     * @param string $queryString
     *
     * @return string
     */
    protected function getQueryStringFromRequest($queryString)
    {
        return $this->getRequestStack()->getCurrentRequest()->get($queryString, '');
    }

    /**
     * Check whether results are sorted by column
     *
     * @param string $column
     *
     * @return bool
     */
    public function checkWhetherResultsAreSortedByColumn($column)
    {
        return stristr($this->getQueryStringFromRequest('sort'), $column) === false ? false : true;
    }

    /**
     * Get column sorted direction
     *
     * @param string $column
     *
     * @return string|bool
     */
    public function getColumnSortedDirection($column)
    {
        $queryStringSort = explode(',', $this->getQueryStringFromRequest('sort'));

        foreach ($queryStringSort as $sort) {

            $data = explode(':', $sort);

            if ($column === $data[0]) {
                return $data[1];
            }
        }

        return false;
    }

    /**
     * @param array $data
     */
    public function renderPaginationTemplate(array $data)
    {
        $this->setData($data);

        $template = 'CekurteGeneratorBundle:Pagination:twitterBootstrap3Pagination.html.twig';

        echo $this->getContainer()->get('templating')->render($template, array(
            'queryStringPageParameterName'  => 'page',
            'route'                         => $this->getRequestStack()->getCurrentRequest()->get('_route'),
            'queries'                       => $this->getRequestStack()->getCurrentRequest()->query->all(),
            'total'                         => $this->getTotal(),
            'current_page_number'           => $this->getCurrentPageNumber(),
            'total_page_number'             => $this->getTotalPageNumber(),
            'is_first_page'                 => $this->isFirstPage(),
            'is_last_page'                  => $this->isLastPage(),
            'has_previous_page'             => $this->hasPreviousPage(),
            'has_next_page'                 => $this->hasNextPage(),
        ));
    }

    /**
     * @param string $field
     * @param string|null $label
     */
    public function renderPaginationSortable($field, $label = null)
    {
        $template = 'CekurteGeneratorBundle:Pagination:twitterBootstrap3Sortable.html.twig';

        echo $this->getContainer()->get('templating')->render($template, array(
            'queryStringSortParameterName'  => 'sort',
            'route'                         => $this->getRequestStack()->getCurrentRequest()->get('_route'),
            'queries'                       => $this->getRequestStack()->getCurrentRequest()->query->all(),
            'field'                         => $field,
            'label'                         => is_null($label) ? $field : $label,
        ));
    }

    /**
     * Build pagination route query string.
     *
     * @param array $queryString
     * @return string
     */
    public function buildPaginationRouteQueryString(array $queryString)
    {
        $data = '?';

        foreach ($queryString as $key => $value) {
            $data .= $key . '=' . $value . '&';
        }

        return substr($data, 0, -1);
    }

    /**
     * Set data
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     */
    public function setData(array $data)
    {
        if (!isset($data['total'])) {
            throw new \InvalidArgumentException('The index "total" cannot be null');
        }

        if (!isset($data['items'])) {
            throw new \InvalidArgumentException('The index "items" cannot be null');
        }

        $this->data = $data;
    }

    /**
     * Get total of the resources to paginate
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->data['total'];
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->data['items'];
    }

    /**
     * Get current page number
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        $currentPageNumber = (int) $this->getQueryStringFromRequest('page');

        return $currentPageNumber === 0 ? 1 : $currentPageNumber;
    }

    /**
     * Get number of resources to show per page
     *
     * @return int
     */
    public function getNumberOfResourcesPerPage()
    {
        $limit = (int) $this->getQueryStringFromRequest('limit');

        return $limit !== 0 ? $limit : 10;
    }

    /**
     * Get total page number
     *
     * @return int
     */
    public function getTotalPageNumber()
    {
        return ceil($this->getTotal() / $this->getNumberOfResourcesPerPage());
    }

    /**
     * Is first page
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCurrentPageNumber() == 1 ? true : false;
    }

    /**
     * Is last page
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->getTotalPageNumber() == $this->getCurrentPageNumber() ? true : false;
    }

    /**
     * Has previous page
     *
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->getCurrentPageNumber() > 1 ? true : false;
    }

    /**
     * Has next page
     *
     * @return bool
     */
    public function hasNextPage()
    {
        return $this->getTotalPageNumber() > $this->getCurrentPageNumber() ? true : false;
    }

    /**
     * Get the unique name of extension
     *
     * @return string
     */
    public function getName()
    {
        return 'cekurte_pagination_extension';
    }
}
