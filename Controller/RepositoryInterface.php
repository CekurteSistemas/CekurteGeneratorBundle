<?php

namespace Cekurte\GeneratorBundle\Controller;

/**
 * Interface que obriga a implementação do método getRepository
 * Isto padroniza a forma como um repositório é obtido dentro de um Controller
 * 
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
interface RepositoryInterface
{
    /**
     * Atalho para retornar uma instância de EntityRepository.
     *
     * @return mixed
     */
    public function getEntityRepository();
}
