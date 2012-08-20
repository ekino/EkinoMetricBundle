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

class ServiceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getDefinition('ekino.metric.pointcut')->getArgument(0);

        foreach ($config['metrics'] as $pos => $conf) {
            list($id, $method) = explode('::', $conf['service']);

            if ($container->hasAlias($id)) {
                $id = $container->getAlias($id);
            }

            $config['metrics'][$pos]['target'] = sprintf('%s::%s', $container->getDefinition($id)->getClass(), $method);
        }

        $this->configurePointcut($config, $container);
        $this->configureInterceptor($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureInterceptor(array $config, ContainerBuilder $container)
    {
        $interceptor = $container->getDefinition('ekino.metric.interceptor');

        $interceptors = array();
        foreach ($config['metrics'] as $data) {
            if (!isset($interceptors[$data['target']])) {
                $interceptors[$data['target']] = array();
            }

            $interceptors[$data['target']][] = array(
                $data['type'], $data['name']
            );
        }

        $interceptor->replaceArgument(0, $interceptors);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configurePointcut(array $config, ContainerBuilder $container)
    {
        $pointcut = $container->getDefinition('ekino.metric.pointcut');

        $pointcuts = array();
        foreach ($config['metrics'] as $data) {
            list($class, $method) = explode('::', $data['target']);

            if (!isset($pointcuts[$class])) {
                $pointcuts[$class] = array();
            }

            $pointcuts[$class][$method] = $method;
        }

        $pointcut->replaceArgument(0, $pointcuts);
    }
}