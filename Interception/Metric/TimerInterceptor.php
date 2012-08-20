<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\Interception\Metric;

use Ekino\Bundle\MetricBundle\Interception\MetricInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Ekino\Metric\MetricManager;

class TimerInterceptor implements MetricInterceptorInterface
{
    protected $stacks;

    protected $metricManager;

    /**
     * @param MetricManager $metricManager
     */
    public function __construct(MetricManager $metricManager)
    {
        $this->stacks = array();
        $this->metricManager = $metricManager;
    }

    /**
     * {@inheritdoc}
     */
    public function preProceed(MethodInvocation $invocation, $name)
    {
        $hash = spl_object_hash($invocation->object);

        if (isset($this->stacks[$hash])) {
            $this->stacks[$hash]['counter']++;
            return;
        }

        $this->stacks[$hash] = array(
            'counter'   => 1,
            'timer'     => new \Ekino\Metric\Type\Timer($name, true)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postProceed(MethodInvocation $invocation, $name)
    {
        $hash = spl_object_hash($invocation->object);

        $this->stacks[$hash]['counter']--;

        if ($this->stacks[$hash]['counter'] === 0) {
            $this->stacks[$hash]['timer']->tick();

            $this->metricManager->add($this->stacks[$hash]['timer']);

            unset($this->stacks[$hash]);
        }
    }
}