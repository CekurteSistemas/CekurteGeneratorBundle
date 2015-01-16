<?php

namespace Cekurte\GeneratorBundle\EventListener;

use Cekurte\ComponentBundle\Util\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Doctrine extension listener
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class DoctrineExtensionListener extends ContainerAware implements ContainerAwareInterface
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $user = $this->getUser();

        if (!is_null($user)) {
            $loggable = $this->getContainer()->get('gedmo.listener.loggable');
            $loggable->setUsername($user->getUsername());
        }
    }
}
