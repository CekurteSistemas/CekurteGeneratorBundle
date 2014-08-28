<?php

namespace Cekurte\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

/**
 * Custom Controller.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class CekurteController extends SymfonyController
{
    /**
     * Get the pagination.
     *
     * @param mixed $data
     * @param int $page
     * @param int|null $resultsPerPage
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPagination($data, $page, $resultsPerPage = null)
    {
        if ($resultsPerPage === null) {
            $resultsPerPage = $this->container->getParameter('paginator_number_results_per_page');
        }

        return $this->get('knp_paginator')->paginate($data, $page, $resultsPerPage);
    }

    /**
     * Create the delete form.
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createDeleteForm()
    {
        return $this->createFormBuilder()->add('id', 'hidden')->getForm();
    }
}
