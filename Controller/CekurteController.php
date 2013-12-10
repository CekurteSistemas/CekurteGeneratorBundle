<?php

namespace Cekurte\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Form;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\ORM\Query;

/**
 * Controller padrão da aplicação.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class CekurteController extends Controller
{
    /**
     * Atalho para retornar uma instância de FlashBag.
     *
     * @return FlashBag
     */
    public function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    /**
     * Atalho para retornar uma instância de Session.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * Atalho para retornar a paginação de registros.
     *
     * @param Query $query
     * @param int $page
     * @param int $resultsPerPage
     *
     * @return PaginationInterface
     */
    public function getPagination(Query $query, $page, $resultsPerPage = null)
    {
        if ($resultsPerPage === null) {
            $resultsPerPage = $this->container->getParameter('paginator_number_results_per_page');
        }

        return $this->get('knp_paginator')->paginate($query, $page, $resultsPerPage);
    }

    /**
     * Cria um formulário para deletar um registro da base de dados.
     *
     * @return Form
     */
    public function createDeleteForm()
    {
        return $this->createFormBuilder()->add('id', 'hidden')->getForm();
    }
}
