<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
