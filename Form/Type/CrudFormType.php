<?php

namespace Cekurte\GeneratorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Crud form type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class CrudFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod($options['method']);

        if ($options['method'] === 'PUT') {
            $builder->add('_method', 'hidden', array(
                'data'   => 'PUT',
                'mapped' => false,
            ));
        }
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'method' => 'POST'
        ));

        $resolver->setAllowedValues(array(
            'method' => array('POST', 'PUT')
        ));
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ck_crud';
    }
}
