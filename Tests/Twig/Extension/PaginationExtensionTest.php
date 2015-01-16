<?php

namespace Cekurte\GeneratorBundle\Tests\Twig\Extension;

use Cekurte\GeneratorBundle\Twig\Extension\PaginationExtension;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PaginationExtensionTest
 */
class PaginationExtensionTest extends TestCase
{
    /**
     * @var PaginationExtension
     */
    private $pagination;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->pagination = null;
    }

    /**
     * Configure container with query string sort
     *
     * @param string $sortValue
     * @param int $pageValue
     */
    private function configureContainerWithQueryStringSort($sortValue, $pageValue = 1)
    {
        $request = new Request();
        $request->query->set('sort', $sortValue);
        $request->query->set('page', $pageValue);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->pagination = new PaginationExtension($requestStack);
    }

    /**
     * Configure pagination data
     *
     * @param string $sortValue
     */
    private function configurePaginationData($total)
    {
        $data = array(
            'total' => $total,
            'items' => array(),
        );

        for ($i = 0; $i < (($total < 10) ? $total : 10); $i++) {
            $data['items'][$i] = rand(1,100);
        }

        $this->pagination->setData($data);
    }

    public function testCheckWhetherResultsAreSortedByColumnCkIdAndDirectionAsc()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc');

        $this->assertTrue($this->pagination->checkWhetherResultsAreSortedByColumn('ck.id'));
        $this->assertEquals('asc', $this->pagination->getColumnSortedDirection('ck.id'));
    }

    public function testCheckWhetherResultsAreSortedByColumnCkIdAndDirectionDesc()
    {
        $this->configureContainerWithQueryStringSort('ck.id:desc');

        $this->assertTrue($this->pagination->checkWhetherResultsAreSortedByColumn('ck.id'));
        $this->assertEquals('desc', $this->pagination->getColumnSortedDirection('ck.id'));
    }

    public function testCheckWhetherResultsAreSortedByColumnCkIdAndCkTitleDirectionAscAndDesc()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc,ck.title:desc');

        $this->assertTrue($this->pagination->checkWhetherResultsAreSortedByColumn('ck.id'));
        $this->assertTrue($this->pagination->checkWhetherResultsAreSortedByColumn('ck.title'));
        $this->assertEquals('asc', $this->pagination->getColumnSortedDirection('ck.id'));
        $this->assertEquals('desc', $this->pagination->getColumnSortedDirection('ck.title'));
    }

    public function testPaginationWith15Resources()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc,ck.title:desc');

        $this->configurePaginationData(15);

        $this->assertEquals(15, $this->pagination->getTotal());
        $this->assertEquals(10, count($this->pagination->getItems()));
        $this->assertEquals(1, $this->pagination->getCurrentPageNumber());
        $this->assertEquals(2, $this->pagination->getTotalPageNumber());

        $this->assertTrue($this->pagination->isFirstPage());
        $this->assertFalse($this->pagination->isLastPage());

        $this->assertFalse($this->pagination->hasPreviousPage());
        $this->assertTrue($this->pagination->hasNextPage());
    }

    public function testPaginationWith5Resources()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc,ck.title:desc');

        $this->configurePaginationData(5);

        $this->assertEquals(5, $this->pagination->getTotal());
        $this->assertEquals(5, count($this->pagination->getItems()));
        $this->assertEquals(1, $this->pagination->getCurrentPageNumber());
        $this->assertEquals(1, $this->pagination->getTotalPageNumber());

        $this->assertTrue($this->pagination->isFirstPage());
        $this->assertTrue($this->pagination->isLastPage());

        $this->assertFalse($this->pagination->hasPreviousPage());
        $this->assertFalse($this->pagination->hasNextPage());
    }

    public function testPaginationWith25ResourcesPageOne()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc,ck.title:desc');

        $this->configurePaginationData(25);

        $this->assertEquals(25, $this->pagination->getTotal());
        $this->assertEquals(10, count($this->pagination->getItems()));
        $this->assertEquals(1, $this->pagination->getCurrentPageNumber());
        $this->assertEquals(3, $this->pagination->getTotalPageNumber());

        $this->assertTrue($this->pagination->isFirstPage());
        $this->assertFalse($this->pagination->isLastPage());

        $this->assertFalse($this->pagination->hasPreviousPage());
        $this->assertTrue($this->pagination->hasNextPage());
    }

    public function testPaginationWith25ResourcesPageTwo()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc,ck.title:desc', 2);

        $this->configurePaginationData(25);

        $this->assertEquals(25, $this->pagination->getTotal());
        $this->assertEquals(10, count($this->pagination->getItems()));
        $this->assertEquals(2, $this->pagination->getCurrentPageNumber());
        $this->assertEquals(3, $this->pagination->getTotalPageNumber());

        $this->assertFalse($this->pagination->isFirstPage());
        $this->assertFalse($this->pagination->isLastPage());

        $this->assertTrue($this->pagination->hasPreviousPage());
        $this->assertTrue($this->pagination->hasNextPage());
    }

    public function testPaginationWith25ResourcesPageThree()
    {
        $this->configureContainerWithQueryStringSort('ck.id:asc,ck.title:desc', 3);

        $this->configurePaginationData(25);

        $this->assertEquals(25, $this->pagination->getTotal());
        $this->assertEquals(10, count($this->pagination->getItems()));
        $this->assertEquals(3, $this->pagination->getCurrentPageNumber());
        $this->assertEquals(3, $this->pagination->getTotalPageNumber());

        $this->assertFalse($this->pagination->isFirstPage());
        $this->assertTrue($this->pagination->isLastPage());

        $this->assertTrue($this->pagination->hasPreviousPage());
        $this->assertFalse($this->pagination->hasNextPage());
    }
}
