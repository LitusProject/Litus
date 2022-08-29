<?php

namespace CommonBundle\Component\Google\ServiceManager;

use CommonBundle\Component\Controller\Exception\RuntimeException;
use Google\Service;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ReflectionClass;

/**
 * Factory to create a Google service instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ServiceFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Service
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['google'])) {
            throw new RuntimeException('Could not find Google config');
        }
        $service = (new ReflectionClass($requestedName))->getShortName();
        $service = strtolower($service);

        if (!isset($config['google']['services'][$service])) {
            throw new RuntimeException('Could not find service config');
        }

        $client = $container->get('google_client');
        foreach ($config['google']['services'][$service]['scopes'] as $scope) {
            $client->addScope($scope);
        }

        $client->authorize();

        return new $requestedName($client);
    }
}
