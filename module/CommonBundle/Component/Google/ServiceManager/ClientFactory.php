<?php

namespace CommonBundle\Component\Google\ServiceManager;

use Google\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory to create a Google client instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ClientFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Client
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['google'])) {
            throw new RuntimeException('Could not find Google config');
        }

        $client = new Google\Client();
        $client->setAuthConfig($config['google']['auth']);

        return $client;
    }
}
