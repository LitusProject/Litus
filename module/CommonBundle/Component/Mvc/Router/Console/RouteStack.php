<?php

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
     * @return RouteStackInterface
     */
    public function addRoute($name, $route, $priority = null)
    {
    }

    /**
     * Add multiple routes to the stack.
     *
     * @param  array|\Traversable $routes
     * @return RouteStackInterface
     */
    public function addRoutes($routes)
    {
    }

    /**
     * Remove a route from the stack.
     *
     * @param  string $name
     * @return RouteStackInterface
     */
    public function removeRoute($name)
    {
    }

    /**
     * Remove all routes from the stack and set new ones.
     *
     * @param  array|\Traversable $routes
     * @return RouteStackInterface
     */
    public function setRoutes($routes)
    {
    }
}
