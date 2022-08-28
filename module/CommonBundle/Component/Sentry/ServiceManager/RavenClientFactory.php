<?php

namespace CommonBundle\Component\Sentry\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
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

        return new Raven_Client(
            $config['sentry']['dsn'],
            $config['sentry']['options']
        );
    }
}
