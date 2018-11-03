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

namespace CommonBundle\Component\Doctrine\Common\Cache\ServiceManager;

use Doctrine\Common\Cache\MemcachedCache;
use Interop\Container\ContainerInterface;
use Memcached;
use RuntimeException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create the Doctrine memcached cache instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MemcachedCacheFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return MemcachedCache
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!extension_loaded('memcached')) {
            throw new RuntimeException('The memcached extension is not loaded');
        }

        $config = $container->get('config');
        if (!isset($config['doctrine']['cache']['memcached'])) {
            throw new RuntimeException('Could not find Doctrine memcached configuration');
        }

        $memcachedConfig = $config['doctrine']['cache']['memcached'];

        $memcached = new Memcached();
        if (!$memcached->addServers($memcachedConfig['servers'])) {
            throw new RuntimeException('Failed to connect to the memcached servers');
        }

        $memcachedCache = new MemcachedCache();
        $memcachedCache->setNamespace($memcachedConfig['namespace']);
        $memcachedCache->setMemcached($memcached);

        return $memcachedCache;
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return MemcachedCache
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'Doctrine\Common\Cache\MemcachedCache');
    }
}
