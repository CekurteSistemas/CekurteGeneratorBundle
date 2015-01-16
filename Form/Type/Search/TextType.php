<?php

namespace Cekurte\GeneratorBundle\Form\Type\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Search form text type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class TextType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'alias'     => $options['alias'],
            'property'  => $options['property'],
            'operation' => $options['operation'],
        ));
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required'  => false,
            'alias'     => 'ck',
            'operation' => 'eq',
        ));

        $resolver->setRequired(array(
            'property'
        ));
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ck_search_text';
    }
}
