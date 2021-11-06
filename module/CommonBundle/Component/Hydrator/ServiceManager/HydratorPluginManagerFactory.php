<?php

namespace CommonBundle\Component\Hydrator\ServiceManager;

use CommonBundle\Component\Hydrator\HydratorPluginManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to instantiate a hydrator plugin manager.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HydratorPluginManagerFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return HydratorPluginManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new HydratorPluginManager($container);
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return HydratorPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Hydrator\HydratorPluginManager');
    }
}
