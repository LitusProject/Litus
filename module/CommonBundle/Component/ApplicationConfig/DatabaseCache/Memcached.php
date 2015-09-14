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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\ApplicationConfig\DatabaseCache;

use Doctrine\Common\Cache\MemcachedCache,
    Memcached as MemcachedObject,
    RuntimeException,
    Zend\ServiceManager\ServiceLocatorInterface;

class Memcached implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        if (!extension_loaded('memcached')) {
            throw new RuntimeException('Litus requires the memcached extension to be loaded');
        }

        $cache = new MemcachedCache();
        $cache->setNamespace(getenv('ORGANIZATION') . '_LITUS');
        $memcached = new MemcachedObject();

        if (!$memcached->addServer('localhost', 11211)) {
            throw new RuntimeException('Failed to connect to the memcached server');
        }

        $cache->setMemcached($memcached);

        return $cache;
    }
}
