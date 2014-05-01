<?php

namespace Cekurte\GeneratorBundle\Twig\Extension;

use Cekurte\GeneratorBundle\Twig\Extension\RoleExtension;

/**
 * Mostra o nome do bundle, do controller e da action que está sendo executada.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class ActionExtension extends RoleExtension
{
    /**
     * @inherited
     */
    public function getFunctions()
    {
        return array(
            'bundle_name'       => new \Twig_Function_Method($this, 'getPublicBundleName'),
            'controller_name'   => new \Twig_Function_Method($this, 'getPublicControllerName'),
            'action_name'       => new \Twig_Function_Method($this, 'getPublicActionName'),
        );
    }

    /**
     * @inherited
     */
    public function getPublicBundleName()
    {
        return str_replace('\\', '', strtolower($this->getBundleName()));
    }

    /**
     * @inherited
     */
    public function getPublicControllerName()
    {
        return strtolower($this->getControllerName());
    }

    /**
     * @inherited
     */
    public function getPublicActionName()
    {
        return $this->getActionName();
    }

    /**
     * Recupera o nome da extensão do twig
     *
     * @return string
     */
    public function getName()
    {
        return 'cekurte_action_extension';
    }
}
