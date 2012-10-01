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
            'on_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/on[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'on_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_slug' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/on/slug[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-z0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_slug',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'on_redirect' => array(
                'type'    => 'Zend\Mvc\Router\Http\Hostname',
                'options' => array(
                    'route' => '/on[/:name][/]',
                    'constraints' => array(
                        'name'  => '[a-zA-Z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'on_redirect',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'on_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'OnBundle\Document' => 'odm_annotation_driver'
                ),
            ),
            'odm_annotation_driver' => array(
                'paths' => array(
                    'onbundle' => __DIR__ . '/../../Document',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'on_install'  => 'OnBundle\Controller\Admin\InstallController',
            'admin_slug'  => 'OnBundle\Controller\Admin\SlugController',

            'on_redirect' => 'OnBundle\Controller\RedirectController',
        ),
    ),
);
