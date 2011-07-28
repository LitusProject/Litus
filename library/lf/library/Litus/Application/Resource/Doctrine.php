<?php

namespace Litus\Application\Resource;

use \Doctrine\Common\ClassLoader;
use \Doctrine\ORM\EntityManager;

use \Zend\Registry;

class Doctrine extends \Zend\Application\Resource\AbstractResource
{
    /**
     * Registry key for the Doctrine EntityManager.
     */
    const REGISTRY_KEY = 'EntityManager';

    /**
     * @var \Doctrine\ORM\EntityManager Doctrine EntityManager
     */
    protected static $_doctrine = null;

    /**
     * Initialize Doctrine
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function init()
    {
        return $this->createEntityManager();
    }

    /**
     * Create a Doctrine EntityManager, using the configuration options.
     *
     * @throws \Zend\Application\Resource\Exception\InitializationException
     * @return \Doctrine\ORM\EntityManager
     */
    public function createEntityManager()
    {
        if (null === self::$_doctrine) {
            $options = $this->getOptions();

            if (!array_key_exists('lib', $options)
                || !array_key_exists('connection', $options)
                || !array_key_exists('config', $options)
            ) {
                throw new \Zend\Application\Resource\Exception\InitializationException(
                    'The configuration is incomplete; please make sure you provided all sections'
                );
            }

            require_once realpath($options['lib']['common']) . '/Doctrine/Common/ClassLoader.php';

            $classLoader = new ClassLoader(
                'Doctrine\Common', realpath($options['lib']['common'])
            );
            $classLoader->register();

            $classLoader = new ClassLoader(
                'Doctrine\DBAL', realpath($options['lib']['dbal'])
            );
            $classLoader->register();

            $classLoader = new ClassLoader(
                'Doctrine\ORM', realpath($options['lib']['orm'])
            );
            $classLoader->register();

            $classLoader = new ClassLoader('Symfony', realpath($options['lib']['symfony']));
            $classLoader->register();

            $classLoader = new ClassLoader(
                $options['config']['entityNamespace'], realpath($options['config']['entityDir'])
            );
            $classLoader->register();

            $classLoader = new ClassLoader(
                $options['config']['proxyNamespace'], realpath($options['config']['proxyDir'])
            );
            $classLoader->register();

            $classLoader = new ClassLoader(
                $options['config']['repositoryNamespace'], realpath($options['config']['repositoryDir'])
            );
            $classLoader->register();

            $config = new \Doctrine\ORM\Configuration();

            $config->setProxyDir(
                $options['config']['proxyDir'] . DIRECTORY_SEPARATOR . str_replace(
                    '\\', DIRECTORY_SEPARATOR, $options['config']['proxyNamespace']
                )
            );
            $config->setProxyNamespace($options['config']['proxyNamespace']);
            if (isset($options['config']['autoGenerateProxyClasses'])) {
                $config->setAutoGenerateProxyClasses($options['config']['autoGenerateProxyClasses']);
            }

            $config->addEntityNamespace($options['config']['entityNamespace'], $options['config']['entityNamespace']);

            $driverImpl = $config->newDefaultAnnotationDriver(array(realpath($options['config']['entityDir'])));
            $config->setMetadataDriverImpl($driverImpl);

            $config->setMetadataCacheImpl(new $options['config']['cache']['metadata']['driver']());
            $config->setQueryCacheImpl(new $options['config']['cache']['query']['driver']());

            self::$_doctrine = EntityManager::create($options['connection'], $config);
            Registry::set(self::REGISTRY_KEY, self::$_doctrine);
        }

        return self::$_doctrine;
    }
}