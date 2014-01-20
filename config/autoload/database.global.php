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

if (!file_exists(__DIR__ . '/../database.config.php')) {
    throw new RuntimeException(
        'The database configuration file (' . (__DIR__ . '/../database.config.php') . ') was not found'
    );
}

$databaseConfig = include __DIR__ . '/../database.config.php';

return array(
    'service_manager' => array(
        'factories' => array(
            'doctrine.cache.orm_default' => function ($serviceManager) {
                if ('production' == getenv('APPLICATION_ENV')) {
                    if (!extension_loaded('memcached'))
                        throw new \RuntimeException('Litus requires the memcached extension to be loaded');

                    $cache = new \Doctrine\Common\Cache\MemcachedCache();
                    $cache->setNamespace('Litus');
                    $memcached = new \Memcached();

                    if(!$memcached->addServer('localhost', 11211))
                        throw now \RuntimeException('Failed to connect to the memcached server');

                    $cache->setMemcached($memcached);
                } else {
                    $cache = new \Doctrine\Common\Cache\ArrayCache();
                }

                return $cache;
            }
        )
    ),
    'doctrine' => array(
        'cache' => array(
            'memcached' => array(
                'namespace' => 'Litus',
            ),
            'array' => array(
                'namespace' => 'Litus',
            ),
        ),
        'configuration' => array(
            'odm_default' => array(
                  //'generate_proxies'   => ('development' == getenv('APPLICATION_ENV')),
                  'generate_proxies'   => true,
                  'proxy_dir'          => 'data/proxies',

                  //'generate_hydrators' => ('development' == getenv('APPLICATION_ENV')),
                  'generate_hydrators' => true,
                  'hydrator_dir'       => 'data/hydrators',

                  'default_db'         => $databaseConfig['document']['dbname'],
            ),
            'orm_default' => array(
                'generate_proxies' => ('development' == getenv('APPLICATION_ENV')),
                'proxyDir'         => 'data/proxies/',

                'metadataCache'    => 'orm_default',
                'queryCache'       => 'orm_default',
                'resultCache'      => 'orm_default',
            )
        ),
        'connection' => array(
            'odm_default' => array(
                'server'   => $databaseConfig['document']['server'],
                'port'     => $databaseConfig['document']['port'],
                'user'     => $databaseConfig['document']['user'],
                'password' => $databaseConfig['document']['password'],
                'dbname'   => $databaseConfig['document']['dbname'],
                'options'  => $databaseConfig['document']['options'],
            ),
            'orm_default' => array(
                'driverClass' => $databaseConfig['relational']['driver'],
                'params' => array(
                    'host'     => $databaseConfig['relational']['host'],
                    'port'     => $databaseConfig['relational']['port'],
                    'user'     => $databaseConfig['relational']['user'],
                    'password' => $databaseConfig['relational']['password'],
                    'dbname'   => $databaseConfig['relational']['dbname'],
                ),
            ),
        ),
        'driver' => array(
            'odm_annotation_driver' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
            ),
            'orm_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            ),
        ),
    ),
);