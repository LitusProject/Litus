<?php

namespace CommonBundle\Component\Module\ServiceManager;

use CommonBundle\Component\Module\AbstractInstaller;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Abstract factory instantiating an installer.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AbstractInstallerFactory implements AbstractFactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @return boolean
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (class_exists($requestedName)) {
            return in_array(AbstractInstaller::class, class_parents($requestedName), true);
        }

        return false;
    }

    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return AbstractInstaller
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $installer = new $requestedName();
        if ($installer instanceof ServiceLocatorAwareInterface) {
            $installer->setServiceLocator($container);
        }

        return $installer;
    }
}
