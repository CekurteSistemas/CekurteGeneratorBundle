<?php

namespace Cekurte\GeneratorBundle\Form\Type;

use Cekurte\GeneratorBundle\Doctrine\FilteredQueryStringRepository as Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Search form type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class SearchFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($options) {

                if (!empty($options['filters'])) {

                    $filters = explode(
                        Filter::QUERYSTRING_SEPARATOR_EXPRESSION,
                        $options['filters']
                    );

                    foreach ($filters as $item) {

                        $pieces = explode(
                            Filter::QUERYSTRING_SEPARATOR_OPERATION,
                            $item
                        );

                        $fieldName = substr(
                            $pieces[0], 1 + strpos(
                                $pieces[0],
                                Filter::QUERYSTRING_SEPARATOR_FIELD
                            )
                        );

                        if ($event->getForm()->has($fieldName)) {
                            $event->getForm()->get($fieldName)->setData($pieces[2]);
                        } elseif (in_array($pieces[1], array('gt', 'gte', 'lt', 'lte'))) {
                            if ($event->getForm()->has($fieldName . 'Start') and $event->getForm()->has($fieldName . 'End')) {

                                $date = \Datetime::createFromFormat('U', DateType::getFormatter()->parse($pieces[2]));

                                if ($date !== false) {
                                    if (in_array($pieces[1], array('gt', 'gte'))) {
                                        $event->getForm()->get($fieldName . 'Start')->setData($pieces[2]);
                                    } else {
                                        $event->getForm()->get($fieldName . 'End')->setData($pieces[2]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'filters'
        ));

        $resolver->setAllowedTypes(array(
            'filters' => array('null', 'string')
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
        return 'ck_search';
    }
}
