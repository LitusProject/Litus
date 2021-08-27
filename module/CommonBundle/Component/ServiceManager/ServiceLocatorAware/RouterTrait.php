<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait RouterTrait
{
    /**
     * @return \Laminas\Router\RouteStackInterface
     */
    public function getRouter()
    {
        return $this->getServiceLocator()->get('router');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
