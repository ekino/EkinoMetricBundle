<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\Tests\Interception;

use Ekino\Bundle\MetricBundle\Interception\MetricInterceptor;
use CG\Proxy\MethodInvocation;

class MetricInterceptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testInterceptionInvalid()
    {
        $stringHelper     = new \Ekino\Metric\StringHelper();
        $reporter         = $this->getMock('Ekino\Metric\Reporter\ReporterInterface');
        $metricManager    = new \Ekino\Metric\MetricManager($reporter);
        $interceptor      = new MetricInterceptor(array(), $metricManager, $stringHelper);
        $object           = new FooBarInterceptor();

        $reflection       = new \ReflectionClass($object);
        $methodInvocation = new MethodInvocation($reflection, $reflection->getMethod('foo'), $object, array(), array());

        $interceptor->intercept($methodInvocation);
    }

    public function testInterception()
    {
        $stringHelper     = new \Ekino\Metric\StringHelper();
        $reporter         = $this->getMock('Ekino\Metric\Reporter\ReporterInterface');
        $metricManager    = new \Ekino\Metric\MetricManager($reporter);
        $classes          = array(
            'Ekino\Bundle\MetricBundle\Tests\Interception\FooBarInterceptor::foo' => array(array('timer', 'controller.name.foo'))
        );

        $interceptor      = new MetricInterceptor($classes, $metricManager, $stringHelper);
        $reflection       = new \ReflectionClass(new FooBarInterceptor);
        $object           = new FooBarInterceptor();
        $methodInvocation = new MethodInvocation($reflection, $reflection->getMethod('foo'), $object, array(), array());

        $interceptor->intercept($methodInvocation);

        $metrics = $metricManager->getMetrics();

        $this->assertEquals(1, count($metrics));
        $this->assertEquals(2, count($metrics[0]));
        $this->assertInstanceOf('Ekino\Metric\Type\TimerInterface', $metrics[0][0]);
        $this->assertEquals($metrics[0][0]->getName(), 'controller.name.foo');
    }
}

class FooBarInterceptor
{
    public function foo($i = 0) {
        usleep(50000);

        if ($i > 2) {
            return;
        }

        $this->foo($i + 1);
    }

    public function bar() {}
}