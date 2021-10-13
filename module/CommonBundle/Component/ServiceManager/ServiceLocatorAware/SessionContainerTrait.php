<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait SessionContainerTrait
{
    /**
     * @return \Laminas\Session\Container
     */
    public function getSessionContainer()
    {
        return $this->getServiceLocator()->get('session_container');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
