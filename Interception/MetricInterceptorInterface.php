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

use CG\Proxy\MethodInvocation;

interface MetricInterceptorInterface
{
    /**
     * Method call before the method is being proceed
     *
     * @param MethodInvocation $invocation
     * @param string           $name
     *
     * @return void
     */
    function preProceed(MethodInvocation $invocation, $name);

    /**
     * Method call after the method is being proceed
     *
     * @param MethodInvocation $invocation
     * @param string           $name
     *
     * @return void
     */
    function postProceed(MethodInvocation $invocation, $name);
}