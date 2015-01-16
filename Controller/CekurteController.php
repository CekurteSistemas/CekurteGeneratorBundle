<?php

namespace Cekurte\GeneratorBundle\Controller;

use Cekurte\ComponentBundle\Entity\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Cekurte Controller.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class CekurteController extends Controller implements RepositoryInterface
{
    /**
     * Create the delete form.
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createDeleteForm()
    {
        return $this->createFormBuilder()->add('id', 'hidden')->getForm();
    }

    /**
     * @inheritdoc
     */
    public function getEntityRepository($persistentObjectName, $persistentManagerName = null)
    {
        return $this->getDoctrine()->getRepository($persistentObjectName, $persistentManagerName);
    }
}
