<?php

namespace Cekurte\GeneratorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * ContainerTestCase
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class ContainerTestCase extends WebTestCase
{
    /**
     * {@inherited}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();

        static::$kernel->boot();
    }

    /**
     * @var ContainerInterface
     */
    public function getContainer()
    {
        return static::$kernel->getContainer();
    }

    /**
     * @var EntityManager
     */
    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}