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
     * @param string $specificRole
     * @param string $genericRole
     *
     * @return boolean
     */
    public function isGranted($specificRole, $genericRole = null)
    {
        $specificRoleSuffix = (empty($specificRole) or 'LIST' === strtoupper($specificRole))
            ? ''
            : self::ROLE_SEPARATOR . strtoupper($specificRole)
        ;

        $specificRoleFormatted = ''
            . self::ROLE_PREFIX
            . self::ROLE_SEPARATOR
            . str_replace('\\', '', strtoupper($this->getBundleName()))
            . self::ROLE_SEPARATOR
            . strtoupper($this->getControllerName())
            . $specificRoleSuffix
        ;

        return $this->getSecurity()->isGranted($specificRoleFormatted)
            or $this->getSecurity()->isGranted(
                is_null($genericRole) ? $this->getRoleNameAdmin() : $genericRole
            )
        ;
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
