<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies'  => ('development' == getenv('APPLICATION_ENV')),
                'proxyDir'         => 'data/proxies/',
            )
        ),
        'connection' => array(
            'orm_default' => array(
                'driverClass' => $databaseConfig['driver'],
                'params' => array(
                    'host'     => $databaseConfig['host'],
                    'port'     => $databaseConfig['port'],
                    'user'     => $databaseConfig['user'],
                    'password' => $databaseConfig['password'],
                    'dbname'   => $databaseConfig['dbname'],
                ),
            ),
        ),
        'driver' => array(
            'my_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            ),
        ),
    ),
);