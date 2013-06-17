<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
    'doctrine' => array(
        'cache' => array(
            'apc' => array(
                'namespace' => 'Litus',
            ),
            'array' => array(
                'namespace' => 'Litus',
            ),
        ),
        'configuration' => array(
            'odm_default' => array(
                  'generate_proxies'   => ('development' == getenv('APPLICATION_ENV')),
                  'proxy_dir'          => 'data/proxies',

                  'generate_hydrators' => ('development' == getenv('APPLICATION_ENV')),
                  'hydrator_dir'       => 'data/hydrators',

                  'default_db'         => $databaseConfig['document']['dbname'],
            ),
            'orm_default' => array(
                'generate_proxies' => ('development' == getenv('APPLICATION_ENV')),
                'proxyDir'         => 'data/proxies/',

                'metadataCache'    => ('development' == getenv('APPLICATION_ENV') ? 'array' : 'apc'),
                'queryCache'       => ('development' == getenv('APPLICATION_ENV') ? 'array' : 'apc'),
                'resultCache'      => ('development' == getenv('APPLICATION_ENV') ? 'array' : 'apc'),
            )
        ),
        'connection' => array(
            'odm_default' => array(
                'server'   => $databaseConfig['document']['server'],
                'port'     => $databaseConfig['document']['port'],
                'user'     => $databaseConfig['document']['user'],
                'password' => $databaseConfig['document']['password'],
                'dbname'   => $databaseConfig['document']['dbname']
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