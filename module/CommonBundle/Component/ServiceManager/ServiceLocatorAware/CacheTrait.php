<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait CacheTrait
{
    /**
     * @return \Laminas\Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->getServiceLocator()->get('cache');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
