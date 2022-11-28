<?php

namespace CommonBundle\Component\Authentication\Adapter\Doctrine\ServiceManager;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Credential;
use CommonBundle\Entity\User\Person;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory to instantiate a Doctrine credential adapter.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class CredentialFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Credential
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Credential(
            $container->get('doctrine.entitymanager.orm_default'),
            Person::class,
            'username'
        );
    }
}
