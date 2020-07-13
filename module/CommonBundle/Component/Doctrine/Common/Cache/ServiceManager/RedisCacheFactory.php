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

use Doctrine\Common\Cache\RedisCache;
use Interop\Container\ContainerInterface;
use Redis;
use RedisException;
use RuntimeException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create the Doctrine Redis cache instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RedisCacheFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return RedisCache
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['redis'])) {
            throw new RuntimeException('Could not find Redis configuration');
        }

        $redis = new Redis();

        try {
            if (isset($config['redis']['persistent_id'])) {
                $connect = $redis->pconnect(
                    $config['redis']['host'],
                    $config['redis']['port'],
                    $config['redis']['timeout'],
                    $config['redis']['persistent_id']
                );
            } else {
                $connect = $redis->connect(
                    $config['redis']['host'],
                    $config['redis']['port'],
                    $config['redis']['timeout']
                );
            }

            if (!$connect) {
                throw new RuntimeException('Failed to connect to Redis server');
            }
        } catch (RedisException $e) {
            return null;
        }

        if (isset($config['redis']['database']) && $config['redis']['database'] != 0) {
            $select = $redis->select($config['redis']['database']);

            if (!$select) {
                throw new RuntimeException('Failed to select Redis database');
            }
        }

        $redisCache = new RedisCache();
        $redisCache->setNamespace($config['doctrine']['cache']['redis']['namespace']);
        $redisCache->setRedis($redis);

        return $redisCache;
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return RedisCache
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'Doctrine\Common\Cache\RedisCache');
    }
}
