<?php

namespace CommonBundle\Component\Authentication\ServiceManager;

use CommonBundle\Component\Authentication\Authentication;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create an authentication instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthenticationFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Authentication
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Authentication(
            $container->get('authentication_credential_adapter'),
            $container->get('authentication_service')
        );
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Authentication\Authentication');
    }
}
