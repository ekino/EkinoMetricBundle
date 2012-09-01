<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\NewRelicBundle\Tests\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

use Ekino\Bundle\MetricBundle\Listener\NewRelicListener;
use Ekino\Metric\Reporter\NewRelic\NewRelic;

class RequestListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testSubRequest()
    {
        $interactor = $this->getMock('Ekino\Metric\Reporter\NewRelic\NewRelicInteractorInterface');
        $interactor->expects($this->never())->method('setTransactionName');

        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request();

        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $listener = new NewRelicListener(new NewRelic('App name', 'Token'), $interactor, array(), array());
        $listener->onCoreRequest($event);
    }

    public function testMasterRequest()
    {
        $interactor = $this->getMock('Ekino\Metric\Reporter\NewRelic\NewRelicInteractorInterface');
        $interactor->expects($this->once())->method('setTransactionName');

        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request();

        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $listener = new NewRelicListener(new NewRelic('App name', 'Token'), $interactor, array(), array());
        $listener->onCoreRequest($event);
    }
}