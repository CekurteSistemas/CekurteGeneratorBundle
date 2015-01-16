<?php

namespace Cekurte\GeneratorBundle\Form\Type\Search;

/**
 * Search form date type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class DateType extends \Cekurte\GeneratorBundle\Form\Type\DateType
{
    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'ck_search_text';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ck_search_date';
    }
}
