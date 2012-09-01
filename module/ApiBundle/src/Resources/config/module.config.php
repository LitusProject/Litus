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

return array(
    'router' => array(
        'routes' => array(
            'api_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/install/api',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'api_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_key' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/api/key[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_key',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'api_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/api/auth[/:action]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'api_auth',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'api_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'ApiBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'apibundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'api_install' => 'ApiBundle\Controller\Admin\InstallController',
            'admin_key'   => 'ApiBundle\Controller\Admin\KeyController',

            'api_auth'    => 'ApiBundle\Controller\AuthController',
        ),
    ),
);