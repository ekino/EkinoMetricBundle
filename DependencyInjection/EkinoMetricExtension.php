<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class EkinoMetricExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('core.xml');
        $loader->load('collector.xml');
        $loader->load('reporter.xml');
        $loader->load('newrelic.xml');

        $this->configureCollectd($config, $container);
        $this->configureStatsd($config, $container);
        $this->configureNewRelic($config, $container);

        $container->getDefinition('ekino.metric.manager')
            ->replaceArgument(0, new Reference($config['reporter']));


        // temporary store wrong data, resolved by the compiler pass
        $container->getDefinition('ekino.metric.interceptor')
            ->replaceArgument(0, $config);

        $container->getDefinition('ekino.metric.pointcut')
            ->replaceArgument(0, $config);

        // configure collector
        $container->getDefinition('ekino.metric.collector.doctrine')
            ->replaceArgument(2,$config['collectors']['doctrine']['prefix']);

        $listener = $container->getDefinition('ekino.metric.listener.terminate');

        foreach ($config['collect_from'] as $id) {
            $listener->addMethodCall('addCollector', array(new Reference($id)));
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureCollectd(array $config, ContainerBuilder $container)
    {
        $collectd = $container->getDefinition('ekino.metric.reporter.collectd');

        $udpWriter = new Definition('Ekino\Metric\Writer\UdpWriter', array(
            $config['reporters']['collectd']['udp_host'],
            $config['reporters']['collectd']['udp_port'],
        ));
        $udpWriter->setPublic(false);

        $collectd->replaceArgument(0, $config['reporters']['collectd']['hostname']);
        $collectd->replaceArgument(1, $udpWriter);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureStatsd(array $config, ContainerBuilder $container)
    {
        $collectd = $container->getDefinition('ekino.metric.reporter.statsd');

        $udpWriter = new Definition('Ekino\Metric\Writer\UdpWriter', array(
            $config['reporters']['statsd']['udp_host'],
            $config['reporters']['statsd']['udp_port'],
        ));
        $udpWriter->setPublic(false);

        $collectd->replaceArgument(0, $udpWriter);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureNewRelic(array $config, ContainerBuilder $container)
    {
        $container->getDefinition('ekino.metric.new_relic')
            ->replaceArgument(0, $config['reporters']['newrelic']['application_name'])
            ->replaceArgument(1, $config['reporters']['newrelic']['api_key'])
        ;
    }
}