<?php

namespace Cekurte\GeneratorBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Verifica se o usuário possuí o papel necessário para exibir um recurso da aplicação.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class RoleExtension extends \Twig_Extension
{

    const ROLE_PREFIX           = 'ROLE';
    const ROLE_SEPARATOR        = '_';
    
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
     * Atalho para recuperar uma instância de TemplateReference
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Templating\TemplateReference
     */
    protected function getTemplate() 
    {
        return $this->getRequest()->attributes->get('_template');
    }


    /**
     * Recupera o nome do bundle
     * 
     * @return string
     */
    protected function getBundleName()
    {
        return strtoupper(substr($this->getTemplate()->get('bundle'), 0, -6));
    }
    
    /**
     * Recupera o nome do controller
     * 
     * @return string
     */
    protected function getControllerName()
    {
        return strtoupper($this->getTemplate()->get('controller'));
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
            . $this->getBundleName()
            . self::ROLE_SEPARATOR 
            . $this->getControllerName()
            . $role;
        
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
