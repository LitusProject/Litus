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

return array(
    'service_manager' => array(
        'factories' => array(
            'cache' => function ($serviceManager) {
                if (('production' == getenv('APPLICATION_ENV'))) {
                    if (!extension_loaded('memcached'))
                        throw new \RuntimeException('Litus requires the memcached extension to be loaded');

                    return \Zend\Cache\StorageFactory::factory(
                        array(
                            'adapter' => array(
                                'name' => 'memcached',
                                'options' => array(
                                    'ttl' => 0,
                                    'namespace' => 'Litus',
                                    'servers' => array(
                                        array('localhost', 11211)
                                    )
                                ),
                            ),
                        )
                    );
                } else {
                    return \Zend\Cache\StorageFactory::factory(
                        array(
                            'adapter' => array(
                                'name' => 'memory',
                                'options' => array(
                                    'ttl' => 0,
                                ),
                            ),
                        )
                    );
                }
            },
        ),
    ),
);

return array();
