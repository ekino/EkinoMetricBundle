<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $collector = $container->getDefinition('ekino.metric.collector.doctrine');

        foreach($container->getDefinitions() as $id => $definition) {
            if (!preg_match('/doctrine.dbal.logger.profiling.(?P<name>[a-zA-Z0-9]*)/', $id, $matches)) {
                continue;
            }

            $collector->addMethodCall('addLogger', array($matches['name'], new Reference($id)));
        }
    }
}