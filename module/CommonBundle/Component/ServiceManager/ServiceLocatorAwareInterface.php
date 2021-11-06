<?php

namespace CommonBundle\Component\ServiceManager;

use Laminas\ServiceManager\ServiceLocatorInterface;

interface ServiceLocatorAwareInterface
{
    /**
     * The ServiceLocatorWareInterface was deprecated in ZF3 because the framework
     * maintainers consider it an anti-pattern. However, since our codebase heavily
     * depends on the service locator, we've reintroduced it through an initializer.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator();
}
