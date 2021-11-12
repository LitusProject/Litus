<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait HydratorPluginManagerTrait
{
    /**
     * @return \Laminas\Hydrator\HydratorPluginManager
     */
    public function getHydratorPluginManager()
    {
        return $this->getServiceLocator()->get('hydrator_plugin_manager');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
