<?php

namespace CommonBundle\Component\Hydrator\ServiceManager;

use CommonBundle\Component\Hydrator\HydratorPluginManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
