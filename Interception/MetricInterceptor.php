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

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Ekino\Metric\MetricManager;
use Ekino\Metric\StringHelper;

class MetricInterceptor implements MethodInterceptorInterface
{
    protected $classes;

    protected $types;

    protected $stacks;

    protected $metricManager;

    protected $stringHelper;

    /**
     * @param array         $classes
     * @param MetricManager $metricManager
     * @param StringHelper  $stringHelper
     */
    public function __construct(array $classes, MetricManager $metricManager, StringHelper $stringHelper)
    {
        $this->classes  = $classes;
        $this->types    = array(
            'timer' => new \Ekino\Bundle\MetricBundle\Interception\Metric\TimerInterceptor($metricManager),
        );
        $this->stringHelper = $stringHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function intercept(MethodInvocation $invocation)
    {
        $signature = sprintf('%s::%s', $invocation->reflectionClass->name, $invocation->reflection->name);

        if (!array_key_exists($signature, $this->classes)) {
            throw new \RuntimeException(sprintf('Unable to incerpt this method %s', $signature));
        }

        $configuration = $this->classes[$signature];

        foreach ($configuration as $pos => $options) {
            list($type, $name) = $options;

            $name = $this->formatName($name, $invocation);

            $configuration[$pos][1] = $name;

            if (!isset($this->types[$type])) {
                continue;
            }

            $this->types[$type]->preProceed($invocation, $name);
        }

        $return = $invocation->proceed();

        foreach ($configuration as $options) {
            list($type, $name) = $options;

            if (!isset($this->types[$type])) {
                continue;
            }

            $this->types[$type]->postProceed($invocation, $name);
        }

        return $return;
    }

    /**
     * @param string           $name
     * @param MethodInvocation $invocation
     */
    protected function formatName($name, MethodInvocation $invocation)
    {
        $placeholders = array(
            '{arg0}' => null,
            '{arg1}' => null,
            '{arg2}' => null,
            '{arg3}' => null,
            '{arg4}' => null,
        );

        foreach ($invocation->arguments as $pos => $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $placeholders['{arg'.$pos.'}'] = $this->stringHelper->convertDot((string) $value);
        }

        return $this->stringHelper->sanitize(strtr($name, $placeholders));
    }
}