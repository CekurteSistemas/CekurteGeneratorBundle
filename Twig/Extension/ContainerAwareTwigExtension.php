<?php

namespace Cekurte\GeneratorBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adiciona o Container a um extensão do Twig
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class ContainerAwareTwigExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Recupera uma instância de ContainerInterface
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Atalho para recuperar uma instância de SecurityContext
     *
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    protected function getSecurity()
    {
        return $this->getContainer()->get('security.context');
    }

    /**
     * Atalho para recuperar uma instância de Request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->getContainer()->get('request');
    }

    /**
     * Atalho para retornar uma instância de Session.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->getContainer()->get('session');
    }

    /**
     * Atalho para retornar uma instância de FlashBag.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBag
     */
    public function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }
}
