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
    return array();

return array(
    'di' => array(
        'definition' => array(
            'class' => array(
                'Zend\Cache\Storage\Adapter\Apc' => array(
                    'instantiator' => array(
                        'Zend\Cache\StorageFactory',
                        'factory'
                    ),
                ),
                'Zend\Cache\StorageFactory' => array(
                    'methods' => array(
                        'factory' => array(
                            'config' => array(
                                'required' => true,
                                'type' => false,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'instance' => array(
            'alias' => array(
                'cache' => 'Zend\Cache\Storage\Adapter\Apc',
            ),
            
            'cache' => array(
                'parameters' => array(
                    'config' => array(
                        'adapter' => array(
                            'name' => 'apc',
                            'options' => array(
                                'ttl' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);