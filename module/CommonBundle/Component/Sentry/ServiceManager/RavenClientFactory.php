<?php

namespace CommonBundle\Component\Sentry\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Raven_Client;
use RuntimeException;

/**
 * Factory to create the Raven_Client instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RavenClientFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Raven_Client
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['sentry'])) {
            throw new RuntimeException('Could not find Sentry config');
        }

        $sentryConfig = $config['sentry'];

        return new Raven_Client(
            $sentryConfig['dsn'],
            $sentryConfig['options']
        );
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Raven_Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'Raven_Client');
    }
}
