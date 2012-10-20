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

if (!extension_loaded('apc'))
    throw new \RuntimeException('Litus requires the APC extension to be loaded');

return array(
    'service_manager' => array(
        'factories' => array(
            'cache' => function ($serviceManager) {
                $cache = \Zend\Cache\StorageFactory::factory(
                    array(
                        'adapter' => array(
                            'name' => 'apc',
                            'options' => array(
                                'ttl' => 0,
                                'namespace' => 'litus_cache',
                            ),
                        ),
                    )
                );
                return $cache;
            },
        ),
    ),
);