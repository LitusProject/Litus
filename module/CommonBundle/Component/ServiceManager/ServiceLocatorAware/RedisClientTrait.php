<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait RedisClientTrait
{
    /**
     * @return \CommonBundle\Component\Redis\Client
     */
    public function getRedisClient()
    {
        return $this->getServiceLocator()->get('redis_client');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
