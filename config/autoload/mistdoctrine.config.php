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
    'di' => array(
        'instance' => array(
            'doctrine_em' => array(
                'parameters' => array(
                    'conn' => array(
                        'driver'   => $databaseConfig['driver'],
                        'host'     => $databaseConfig['host'],
                        'port'     => $databaseConfig['port'], 
                        'user'     => $databaseConfig['user'],
                        'password' => $databaseConfig['password'],
                        'dbname'   => $databaseConfig['dbname'],
                    ),
                ),
            ),
            'doctrine_config' => array(
                'parameters' => array(
                	'autoGenerateProxyClasses' => ('development' == getenv('APPLICATION_ENV')),
                	'proxyDir'                 => realpath('data/proxies'),
                	'entityPaths'              => array(),
                ),
            ), 
        ),
    ),
);