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

return array(
    'router' => array(
        'routes' => array(
            'api_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/api[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'api_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'api_admin_key' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/api/key[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'api_admin_key',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'api_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/api/auth[/:action][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'api_auth',
                    ),
                ),
            ),
            'api_mail' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/api/mail[/:action[/type/:type]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'type'   => '(tar|zip)'
                    ),
                    'defaults' => array(
                        'controller' => 'api_mail',
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
                    'ApiBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'apibundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'api_install'   => 'ApiBundle\Controller\Admin\InstallController',
            'api_admin_key' => 'ApiBundle\Controller\Admin\KeyController',

            'api_auth'      => 'ApiBundle\Controller\AuthController',
            'api_mail'      => 'ApiBundle\Controller\MailController',
        ),
    ),
);
