<?php

namespace Cekurte\GeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CekurteGeneratorBundle extends Bundle
{
    public function getParent()
    {
        return 'SensioGeneratorBundle';
    }
}
