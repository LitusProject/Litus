<?php

namespace CommonBundle\Component\Controller\ServiceManager;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;

class AbstractActionControllerInitializer implements InitializerInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  object             $instance
     * @return void
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof ServiceLocatorAwareInterface) {
            return;
        }

        $instance->setServiceLocator($container);
    }
}
