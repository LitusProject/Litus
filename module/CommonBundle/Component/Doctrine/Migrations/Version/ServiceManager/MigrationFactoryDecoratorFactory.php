<?php

namespace CommonBundle\Component\Doctrine\Migrations\Version\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory to create a Doctrine Migrations migration factory.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

class MigrationFactoryDecoratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return MigrationFactoryDecorator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MigrationFactoryDecorator
    {
        $factory = new DbalMigrationFactory(
            $container->get('doctrine.connection.orm_default'),
            new NullLogger()
        );

        return new MigrationFactoryDecorator($factory);
    }

     /**
     * @param ServiceLocatorInterface $locator
     *
     * @return Raven_Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Raven_Client
    {
        return $this($serviceLocator, 'CommonBundle\Component\Redis\Client');
    }
}
