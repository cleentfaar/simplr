<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Test;

abstract class UnitTest extends Test
{

    /**
     * Example using LiipFunctionalBundle the service mock builder
     */
    /**
    public function testIndexAction()
    {
        $view = $this->getServiceMockBuilder('FooView')->getMock();

        $view->expects($this->once())
            ->method('setTemplate')
            ->with('FooBundle:Default:index.twig')
            ->will($this->returnValue(null))
        ;

        $view->expects($this->once())
            ->method('handle')
            ->with()
            ->will($this->returnValue('success'))
        ;

        $controller = new DefaultController($view);

        $this->assertEquals('success', $controller->indexAction());
    }
     */
}
