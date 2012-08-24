<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ekino\Bundle\MetricBundle\DependencyInjection\Compiler\ServiceCompilerPass;
use Ekino\Bundle\MetricBundle\DependencyInjection\Compiler\DoctrineCompilerPass;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class EkinoMetricBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineCompilerPass, PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new ServiceCompilerPass, PassConfig::TYPE_BEFORE_REMOVING);
    }
}