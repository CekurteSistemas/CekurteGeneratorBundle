<?php

namespace Cekurte\GeneratorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form editor type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class EditorType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'attr'   => array(
                'class' => 'ckeditor',
            ),
        ));
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ck_editor';
    }
}
