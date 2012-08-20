<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\Interception;

use JMS\AopBundle\Aop\PointcutInterface;

class MetricPointcut implements PointcutInterface
{
    protected $classes;

    /**
     * @param array $classes
     */
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function matchesClass(\ReflectionClass $class)
    {
        return array_key_exists($class->name, $this->classes);
    }

    /**
     * {@inheritdoc}
     */
    public function matchesMethod(\ReflectionClass $class, \ReflectionMethod $method)
    {
        return in_array($method->name, $this->classes[$class->name]);
    }
}