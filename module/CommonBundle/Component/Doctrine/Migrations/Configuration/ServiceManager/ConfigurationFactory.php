<?php

namespace CommonBundle\Component\Doctrine\Migrations\Configuration\ServiceManager;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Doctrine\Migrations\Configuration\Configuration;
use Interop\Container\ContainerInterface;

/**
 * Factory to create the Doctrine Migrations configuration instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ConfigurationFactory extends \DoctrineORMModule\Service\MigrationsConfigurationFactory
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configuration = parent::__invoke($container, $requestedName, $options);

        foreach ($configuration->getMigrations() as $version) {
            $migration = $version->getMigration();
            if (!($migration instanceof ServiceLocatorAwareInterface)) {
                continue;
            }

            $migration->setServiceLocator($container);
        }

        return $configuration;
    }
}
