<?php

namespace CommonBundle\Component\Controller\Plugin\ServiceManager;

use CommonBundle\Component\Controller\Plugin\Paginator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory to instantiate a paginator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class PaginatorFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Paginator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $paginator = new Paginator();
        $paginator->setServiceLocator($container);

        return $paginator;
    }
}
