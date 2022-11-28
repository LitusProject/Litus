<?php

namespace CommonBundle\Component\Redis\ServiceManager;

use CommonBundle\Component\Redis\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RuntimeException;

/**
 * Factory to create the Credis_Client instance.
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
        if (!isset($config['redis'])) {
            throw new RuntimeException('Could not find Redis config');
        }

        return new Client($config['redis']);
    }
}
