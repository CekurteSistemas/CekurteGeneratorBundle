<?php

namespace Cekurte\GeneratorBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Gedmo\Mapping\MappedEventSubscriber;

/**
 * Doctrine Extensions SoftDeleteable Listener
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class SoftDeleteableListener extends MappedEventSubscriber implements EventSubscriber
{
    /**
     * @inheritdoc
     */
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return array(
            \Gedmo\SoftDeleteable\SoftDeleteableListener::PRE_SOFT_DELETE,
            \Gedmo\SoftDeleteable\SoftDeleteableListener::POST_SOFT_DELETE,
        );
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preSoftDelete(LifecycleEventArgs $event)
    {
        // ...
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postSoftDelete(LifecycleEventArgs $event)
    {
        $ea = $this->getEventAdapter($event);
        $om = $ea->getObjectManager();

        $reflection = new \ReflectionClass(get_class($event->getObject()));

        if (strpos($reflection->getDocComment(), 'Loggable')) {
            $om->getEventManager()->dispatchEvent(
                Events::postPersist,
                $ea->createLifecycleEventArgsInstance($event->getObject(), $om)
            );
        }
    }
}
