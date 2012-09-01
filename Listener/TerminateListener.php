<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\Listener;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Ekino\Metric\MetricManager;
use Ekino\Metric\Collector\CollectorInterface;
use Ekino\Metric\Collector\CollectionCollectorInterface;

class TerminateListener
{
    protected $manager;

    protected $collectors;

    /**
     * @param MetricManager $manager
     */
    public function __construct(MetricManager $manager)
    {
        $this->manager    = $manager;
        $this->collectors = array();
    }

    /**
     * @param CollectionCollectorInterface $collector
     */
    public function addCollector($collector)
    {
        $this->collectors[] = $collector;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onTerminate(PostResponseEvent $event)
    {
        foreach ($this->collectors as $collector) {
            if ($collector instanceof CollectionCollectorInterface) {
                $this->manager->addCollection($collector->get());
            } elseif ($collector instanceof CollectorInterface) {
                $this->manager->add($collector->get());
            }
        }

        $this->manager->flush();
    }
}