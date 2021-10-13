<?php

namespace CommonBundle\Component\View\Helper\ServiceManager;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Abstract factory instantiating an installer.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AbstractHelperFactory implements AbstractFactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @return boolean
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (class_exists($requestedName)) {
            return in_array(AbstractHelper::class, class_parents($requestedName), true);
        }

        return false;
    }

    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return AbstractHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $helper = new $requestedName($options);
        if ($helper instanceof ServiceLocatorAwareInterface) {
            $helper->setServiceLocator($container);
        }

        return $helper;
    }
}
