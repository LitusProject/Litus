<?php

namespace CommonBundle\Component\Doctrine\Common\Cache\ServiceManager;

use Doctrine\Common\Cache\RedisCache;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Redis;
use RedisException;
use RuntimeException;

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
}
