<?php

namespace Cekurte\GeneratorBundle\Twig\Extension;

/**
 * Adiciona o Doctrine a um extensão do Twig
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class DoctrineAwareTwigExtension extends ContainerAwareTwigExtension
{
    /**
     * Recupera uma instância de ContainerInterface
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * Atalho para recuperar uma instância de SecurityContext
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Atalho para recuperar uma instância de Request
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($repository)
    {
        return $this->getEntityManager()->getRepository($repository);
    }
}
