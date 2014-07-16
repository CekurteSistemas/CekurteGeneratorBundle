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

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
        );
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request');
    }

    /**
     * On PRE_PERSIST event
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $args->getEntity()
            ->setDeleted(false)
            ->setCreatedAt(new \DateTime())
            ->setCreatedBy($this->getUser())
        ;
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

        $args->getEntity()
            ->setUpdatedAt(new \DateTime())
            ->setUpdatedBy($this->getUser())
        ;
    }

    /**
     * On PRE_REMOVE event
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity()
            ->setDeleted(true)
            ->setDeletedAt(new \DateTime())
            ->setDeletedBy($this->getUser())
        ;

        $entityManager = $args->getEntityManager();

        $entityManager->persist($entity);
        $entityManager->flush();

        $entityManager->detach($entity);
    }
}
