<?php

namespace Cekurte\GeneratorBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Verifica se o usuário possuí o papel necessário para exibir um recurso da aplicação.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class RoleExtension extends ContainerAwareTwigExtension
{
    const ROLE_PREFIX           = 'ROLE';
    const ROLE_SEPARATOR        = '_';

    /**
     * Recupera o nome do bundle
     *
     * @return string
     */
    protected function getBundleName()
    {
        $pattern = "/(.*)Bundle/";
        $matches = array();

        preg_match($pattern, $this->getRequest()->get('_controller'), $matches);

        return empty($matches) ? null : $matches[1];
    }

    /**
     * Recupera o nome do controller
     *
     * @return string
     */
    protected function getControllerName()
    {
        $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
        $matches = array();

        preg_match($pattern, $this->getRequest()->get('_controller'), $matches);

        return empty($matches) ? null : $matches[1];
    }

    /**
     * Recupera o nome da action
     *
     * @return string
     */
    protected function getActionName()
    {
        $pattern = "#::([a-zA-Z]*)Action#";
        $matches = array();

        preg_match($pattern, $this->getRequest()->get('_controller'), $matches);

        return empty($matches) ? null : $matches[1];
    }

    /**
     * Recupera o nome do papel utilizado pelo administrador
     *
     * @return string
     */
    protected function getRoleNameAdmin()
    {
        return self::ROLE_PREFIX . self::ROLE_SEPARATOR . 'ADMIN';
    }

    /**
     * @inherited
     */
    public function getFunctions()
    {
        return array(
            'cekurte_is_granted' => new \Twig_Function_Method($this, 'isGranted'),
        );
    }

    /**
     * Verifica se um usuário possuí um papel para acessar um recurso da aplicação.
     *
     * @param string $role
     *
     * @return boolean
     */
    public function isGranted($role)
    {
        $role = (empty($role) or 'LIST' === strtoupper($role)) ? '' : self::ROLE_SEPARATOR . strtoupper($role);

        $role = self::ROLE_PREFIX
            . self::ROLE_SEPARATOR
            . str_replace('\\', '', strtoupper($this->getBundleName()))
            . self::ROLE_SEPARATOR
            . strtoupper($this->getControllerName())
            . $role
        ;

        return ($this->getSecurity()->isGranted($role) or $this->getSecurity()->isGranted($this->getRoleNameAdmin()));
    }

    /**
     * Recupera o nome da extensão do twig
     *
     * @return string
     */
    public function getName()
    {
        return 'cekurte_role_extension';
    }
}
