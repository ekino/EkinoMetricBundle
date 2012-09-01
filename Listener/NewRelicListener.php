<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Ekino\Metric\Reporter\NewRelic\NewRelic;
use Ekino\Metric\Reporter\NewRelic\NewRelicInteractorInterface;

class NewRelicListener
{
    protected $ignoreRoutes;

    protected $ignoreUrls;

    protected $newRelic;

    protected $interactor;

    /**
     * @param NewRelic                    $newRelic
     * @param NewRelicInteractorInterface $interactor
     * @param array                       $ignoreRoutes
     * @param array                       $ignoreUrls
     */
    public function __construct(NewRelic $newRelic, NewRelicInteractorInterface $interactor, array $ignoreRoutes, array $ignoreUrls)
    {
        $this->interactor   = $interactor;
        $this->newRelic     = $newRelic;
        $this->ignoreRoutes = $ignoreRoutes;
        $this->ignoreUrls   = $ignoreUrls;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onCoreRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $route = $event->getRequest()->get('_route');

        $this->interactor->setTransactionName($route ?: 'symfony unknow route');
        $this->interactor->setApplicationName($this->newRelic->getName());
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onCoreResponse(FilterResponseEvent $event)
    {
        foreach ($this->newRelic->getCustomMetrics() as $name => $value) {
            $this->interactor->addCustomMetric($name, $value);
        }

        foreach ($this->newRelic->getCustomParameters() as $name => $value) {
            $this->interactor->addCustomParameter($name, $value);
        }
    }
}