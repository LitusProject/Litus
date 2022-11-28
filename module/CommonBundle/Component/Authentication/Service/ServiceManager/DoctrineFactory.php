<?php

namespace CommonBundle\Component\Authentication\Service\ServiceManager;

use CommonBundle\Component\Authentication\Action\Doctrine as DoctrineAction;
use CommonBundle\Component\Authentication\Service\Doctrine as DoctrineService;
use CommonBundle\Entity\User\Session;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\Storage\Session as SessionStorage;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RuntimeException;

/**
 * Factory to instantiate a Doctrine service.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class DoctrineFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return DoctrineService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['session_config'])) {
            throw new RuntimeException('Could not find session configuration');
        }

        $doctrineAction = new DoctrineAction(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get('mail_transport')
        );

        return new DoctrineService(
            $container->get('doctrine.entitymanager.orm_default'),
            Session::class,
            new SessionStorage('Litus_Auth'),
            'Litus_Auth_Session',
            2678400,
            $config['session_config']['cookie_domain'],
            $config['session_config']['cookie_secure'],
            $doctrineAction
        );
    }
}
