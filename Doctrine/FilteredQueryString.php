<?php

namespace Cekurte\GeneratorBundle\Doctrine;

/**
 * Filtered QueryString
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
interface FilteredQueryString
{
    /**
     * Get a query string fields allowed to filter and sort the resources.
     *
     * @return array
     */
    public function getQueryStringFieldsWhiteList();
}
