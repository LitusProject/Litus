<?php

namespace CommonBundle\Component\Sentry\ServiceManager;

use CommonBundle\Component\Sentry\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Raven_Client;

/**
 * Factory to create a Sentry client instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ClientFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Raven_Client
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Client(
            $container->get('raven_client'),
            $container->get('authentication')
        );
    }
}
