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

namespace CommonBundle\Component\Redis\ServiceManager;

use CommonBundle\Component\Redis\Client;
use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create the Redis client instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ClientFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Client
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!$container->has('redis_config')) {
            throw new RuntimeException('Could not find Redis configuration');
        }

        return new Client($container->get('redis_config'));
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Redis\Client');
    }
}
