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
 
$settings = array(
	'use_annotations' => true,
    'annotation_file' => __DIR__ . '/../../vendor/DoctrineORMModule/vendor/doctrine-orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php',
   	'production' => false,
    'cache' => 'array',
    'memcache' => array( 
        'host' => '127.0.0.1',
        'port' => '11211'
    ),
    'connection' => array(
        'driver'   => 'pdo_pgsql',
        'host'     => 'localhost',
        'port'     => '3306', 
        'user'     => 'litus',
        'password' => 'huQeyU8te3aXusaz',
        'dbname'   => 'litus',
    ),
    'driver' => array(
        'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
        'namespace' => 'Application\Entity',
        'paths'     => array(
        	'module/CommonBundle/src/Entity',
        	'module/CudiBundle/src/Entity',
        )
    ),
    'namespace_aliases' => array(
    	'ORM'
    ),
);

$cache = array('array', 'memcache', 'apc');
if (!in_array($settings['cache'], $cache)) {
    throw new InvalidArgumentException(sprintf(
        'Cache must be one of: %s', implode(', ', $cache)
    ));
}
$settings['cache'] = 'doctrine_cache_' . $settings['cache'];

return array(
    'doctrine_orm_module' => array(
        'annotation_file' => $settings['annotation_file'],
        'use_annotations' => $settings['use_annotations'],
    ),
    'di' => array(
        'instance' => array(
            'doctrine_memcache' => array(
                'parameters' => $settings['memcache'],
            ),
            'orm_config' => array(
                'parameters' => array(
                    'opts' => array(
                        'entity_namespaces' 	=> $settings['namespace_aliases'],
                        'auto_generate_proxies' => !$settings['production'],
                    ),
                    'metadataCache' => $settings['cache'],
                    'queryCache'    => $settings['cache'],
                    'resultCache'   => $settings['cache'],
                )
            ),
            'orm_connection' => array(
                'parameters' => array(
                    'params' => $settings['connection']
                ),
            ),
            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'application_annotation_driver' => $settings['driver'],
                    ),
                    'cache' => $settings['cache']
                ),
            ),
        ),
    ),
);