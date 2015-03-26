<?php

/*
 * This file is part of the Cekurte package.
 *
 * (c) João Paulo Cercal <jpcercal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\GeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CekurteGeneratorBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'SensioGeneratorBundle';
    }
}
