<?php

namespace Cekurte\GeneratorBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Handler padrão para os formulários da aplicação.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class CekurteFormHandler
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var FlashBag
     */
    protected $flashBag;

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param EntityManager $em
     * @param FlashBag $flashBag
     */
    public function __construct(FormInterface $form, Request $request, EntityManager $em, FlashBag $flashBag)
    {
        $this->form = $form;
        $this->request = $request;
        $this->em = $em;
        $this->flashBag = $flashBag;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return FlashBag
     */
    public function getFlashBag()
    {
        return $this->flashBag;
    }

    /**
     * Cria ou atualiza um registro no banco de dados.
     *
     * @return int|boolean O ID do registro afetado no banco de dados, do contrário, false.
     */
    public function save()
    {

        $this->getForm()->bind($this->getRequest());

        if ($this->getForm()->isValid()) {

            $data = $this->getForm()->getData();

            $this->getFlashBag()->add('message', array(
                'type' => 'success',
                'message' => sprintf('The record has been %s successfully.', $data->getId() ? 'updated ' : 'created'),
            ));

            $this->getEm()->persist($data);
            $this->getEm()->flush();

            return $data->getId();
        } else {
            $errors = $this->getForm()->getErrors();

            foreach ($errors as $error) {
                $this->getFlashBag()->add('message', array(
                    'type' => 'error',
                    'message' => $error->getMessage(),
                ));
            }

            return false;
        }

        return false;
    }

    /**
     * Remove um registro do banco de dados.
     *
     * @param string $entityName BundleName:Entity
     *
     * @return boolean True se remover o registro, false do contrário
     */
    public function delete($entityName)
    {

        $this->getForm()->bind($this->getRequest());

        if ($this->getForm()->isValid()) {

            $entity = $this->getEm()->getRepository($entityName)->find($this->getRequest()->request->get('id'));

            if (!$entity) {

                $this->getFlashBag()->add('message', array(
                    'type' => 'error',
                    'message' => 'The record was not found.',
                ));

                return false;
            }

            $this->getEm()->remove($entity);
            $this->getEm()->flush();

            $this->getFlashBag()->add('message', array(
                'type' => 'success',
                'message' => 'The record has been removed successfully.',
            ));

            return true;
        } else {

            $errors = $this->getForm()->getErrors();

            foreach ($errors as $error) {
                $this->getFlashBag()->add('message', array(
                    'type' => 'error',
                    'message' => $error->getMessage(),
                ));
            }

            return false;
        }
    }
}
