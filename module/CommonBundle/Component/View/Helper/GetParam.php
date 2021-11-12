<?php

namespace CommonBundle\Component\View\Helper;

use Laminas\Router\RouteMatch;

/**
 * This view helper makes sure we can access the request paramaters in our view.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GetParam extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * @var RouteMatch The matched router object
     */
    private $routeMatch = null;

    /**
     * @param  RouteMatch $routeMatch The matched router object
     * @return GetParam
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    /**
     * @param  string      $key     The parameter's key
     * @param  string|null $default A default value for when the key is not present
     * @return string
     */
    public function __invoke($key, $default = null)
    {
        if ($this->routeMatch === null) {
            throw new Exception\RuntimeException('No matched route was provided');
        }

        return $this->routeMatch->getParam($key, $default);
    }
}
