<?php

namespace CommonBundle\Component\Session\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;

/**
 * Factory to instantiate the session container.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ContainerFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Container
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Container('Litus');
    }
}
