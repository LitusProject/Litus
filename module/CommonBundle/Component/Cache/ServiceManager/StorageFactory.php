<?php

namespace CommonBundle\Component\Cache\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\StorageFactory as LaminasStorageFactory;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory to instantiate the configured cache storage adapter.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class StorageFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return StorageInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        return LaminasStorageFactory::factory($config['cache']['storage']);
    }
}
