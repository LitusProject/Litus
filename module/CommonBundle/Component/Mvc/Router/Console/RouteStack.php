<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Mvc\Router\Console;

use Zend\Stdlib\RequestInterface as Request,
    Zend\Console\Request as ConsoleRequest,
    Zend\Mvc\Router\Console\RouteMatch,
    Zend\Stdlib\Parameters;

class RouteStack extends \DoctrineModule\Mvc\Router\Console\SymfonyCli implements \Zend\Mvc\Router\RouteStackInterface
{
    /**
     * {@inheritDoc}
     */
    public function match(Request $request)
    {
        if (!$request instanceof ConsoleRequest) {
            return null;
        }

        $params = $request->getParams()->toArray();

        if (! isset($params[0]) || ! $this->cliApplication->has($params[0])) {
            $request->setParams(new Parameters(array('list')));
        }

        return new RouteMatch($this->defaults);
    }

    /**
     * Add a route to the stack.
     *
     * @param  string  $name
     * @param  mixed   $route
     * @param  int $priority
     * @return RouteStack
     */
    public function addRoute($name, $route, $priority = null)
    {
        return $this;
    }

    /**
     * Add multiple routes to the stack.
     *
     * @param  array|\Traversable $routes
     * @return RouteStack
     */
    public function addRoutes($routes)
    {
        return $this;
    }

    /**
     * Remove a route from the stack.
     *
     * @param  string $name
     * @return RouteStack
     */
    public function removeRoute($name)
    {
        return $this;
    }

    /**
     * Remove all routes from the stack and set new ones.
     *
     * @param  array|\Traversable $routes
     * @return RouteStack
     */
    public function setRoutes($routes)
    {
        return $this;
    }
}
