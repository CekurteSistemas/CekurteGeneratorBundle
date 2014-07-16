<?php

namespace Cekurte\GeneratorBundle\EventListener;

use Cekurte\ComponentBundle\Util\ContainerAware;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Doctrine subscriber events
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class DoctrineSubscriber extends ContainerAware implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
        );
    }

    /**
     * Get a Request instance
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request');
    }

    /**
     * Verify if the method exist on object
     *
     * @param mixed $object
     * @param string $methodName
     *
     * @return bool
     */
    protected function methodExists($object, $methodName)
    {
        return method_exists(get_class($object), $methodName);
    }

    /**
     * On PRE_PERSIST event
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($this->methodExists($args->getEntity(), 'setDeleted')) {
            $args->getEntity()->setDeleted(false);
        }

        if ($this->methodExists($args->getEntity(), 'setCreatedAt')) {
            $args->getEntity()->setCreatedAt(new \DateTime());
        }

        if ($this->methodExists($args->getEntity(), 'setCreatedBy')) {
            $args->getEntity()->setCreatedBy($this->getUser());
        }
    }

    /**
     * On PRE_UPDATE event
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        /**
         * Hack: the event Events::preRemove is raised with event
         * Events::preUpdate, then, this is a verification.
         */
        if ($this->getRequest()->isMethod('DELETE')) {
            return;
        }

        if ($this->methodExists($args->getEntity(), 'setUpdatedAt')) {
            $args->getEntity()->setUpdatedAt(new \DateTime());
        }

        if ($this->methodExists($args->getEntity(), 'setUpdatedBy')) {
            $args->getEntity()->setUpdatedBy($this->getUser());
        }
    }

    /**
     * On PRE_REMOVE event
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($this->methodExists($args->getEntity(), 'setDeleted')) {
            $args->getEntity()->setDeleted(true);
        }

        if ($this->methodExists($args->getEntity(), 'setDeletedAt')) {
            $args->getEntity()->setDeletedAt(new \DateTime());
        }

        if ($this->methodExists($args->getEntity(), 'setDeletedBy')) {
            $args->getEntity()->setDeletedBy($this->getUser());
        }

        $entityManager = $args->getEntityManager();

        $entityManager->persist($args->getEntity());
        $entityManager->flush();

        $entityManager->detach($args->getEntity());
    }
}
