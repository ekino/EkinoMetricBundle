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

use Ekino\Bundle\MetricBundle\Interception\MetricPointcut;

class MetricPointcutTest extends \PHPUnit_Framework_TestCase
{
    public function testMatching()
    {
        $pointcut = new MetricPointcut(array(
            'Ekino\Bundle\MetricBundle\Tests\Interception\FooBar' => array(
                'foo',
            )
        ));

        $reflectionClass = new \ReflectionClass('Ekino\Bundle\MetricBundle\Tests\Interception\FooBar');

        $this->assertFalse($pointcut->matchesClass(new \ReflectionClass('PHPUnit_Framework_TestCase')));
        $this->assertTrue($pointcut->matchesClass($reflectionClass));
        $this->assertTrue($pointcut->matchesMethod($reflectionClass, $reflectionClass->getMethod('foo')));
        $this->assertFalse($pointcut->matchesMethod($reflectionClass, $reflectionClass->getMethod('bar')));
    }
}

class FooBar
{
    public function foo() {}
    public function bar() {}
}