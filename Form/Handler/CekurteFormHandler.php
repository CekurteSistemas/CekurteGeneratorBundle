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
     * @param Request       $request
     * @param EntityManager $em
     * @param FlashBag      $flashBag
     */
    public function __construct(FormInterface $form, Request $request, EntityManager $em, FlashBag $flashBag)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->em       = $em;
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
    public function getManager()
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
     * Verifica se o formulário submetido é válido
     *
     * @return boolean
     */
    protected function formIsValid()
    {
        $this->getForm()->handleRequest($this->getRequest());

        if ($this->getForm()->isValid()) {
            return true;
        }

        $errors = $this->getForm()->getErrors();

        foreach ($errors as $error) {
            $this->getFlashBag()->add('message', array(
                'type'      => 'error',
                'message'   => $error->getMessage(),
            ));
        }

        return false;
    }

    /**
     * Cria ou atualiza um registro no banco de dados.
     *
     * @return int|boolean O ID do registro afetado no banco de dados, do contrário, false.
     */
    public function save()
    {
        if ($this->formIsValid()) {

            $data = $this->getForm()->getData();

            $this->getFlashBag()->add('message', array(
                'type'      => 'success',
                'message'   => sprintf('The record has been %s successfully.', $data->getId() ? 'updated ' : 'created'),
            ));

            $this->getManager()->persist($data);
            $this->getManager()->flush();

            return $data->getId();
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
        if ($this->formIsValid()) {

            $entity = $this->getManager()->getRepository($entityName)->find(
                $this->getRequest()->request->get('id')
            );

            if (!$entity) {

                $this->getFlashBag()->add('message', array(
                    'type'      => 'error',
                    'message'   => 'The record was not found.',
                ));

                return false;
            }

            $this->getManager()->remove($entity);
            $this->getManager()->flush();

            $this->getFlashBag()->add('message', array(
                'type'      => 'success',
                'message'   => 'The record has been removed successfully.',
            ));

            return true;
        }

        return false;
    }
}
