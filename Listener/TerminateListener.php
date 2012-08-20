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

class TerminateListener
{
    protected $manager;

    /**
     * @param MetricManager $manager
     */
    public function __construct(MetricManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onTerminate(PostResponseEvent $event)
    {
        $this->manager->flush();
    }
}