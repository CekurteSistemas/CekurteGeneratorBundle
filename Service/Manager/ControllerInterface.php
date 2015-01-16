<?php

namespace Cekurte\GeneratorBundle\Service\Manager;

use Cekurte\GeneratorBundle\Service\ManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ControllerInterface
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
interface ControllerInterface
{
    /**
     * Get the manager.
     *
     * @return ManagerInterface
     */
    public function getManager();
}
