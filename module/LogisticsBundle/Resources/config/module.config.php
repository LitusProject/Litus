<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
            'logistics_install' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/logistics[/]',
                    'defaults' => array(
                        'controller' => 'logistics_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'logistics_admin_driver' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/driver[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'logistics_admin_driver',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'logistics_admin_van_reservation' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/van_reservation[/:action[/:id][/page/:page][/return/:return]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'return'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'logistics_admin_van_reservation',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'logistics_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/logistics[/:action][/date/:date][/:id][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                        'date'     => '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}',
                        'id'       => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'logistics_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'logistics_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/logistics/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'logistics_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'logistics_reservation_fetch' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/logistics/fetch[/:start][/:end][/]',
                    'constraints' => array(
                        'start' => '[0-9]*',
                        'end'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'logistics_index',
                        'action'     => 'fetch',
                    ),
                ),
            ),
            'logistics_lease' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'       => '[/:language]/logistics/lease[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults'    => array(
                        'controller' => 'logistics_lease',
                        'action'     => 'index',
                    ),
                ),
            ),
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'logistics_layouts' => __DIR__ . '/../layouts',
            'logistics_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'LogisticsBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'logisticsbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/logistics.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/logistics.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'logisticsbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'logistics_css' => array(
                        'assets' => array(
                            'logistics/less/base.less',
                        ),
                        'filters' => array(
                            'logistics_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'logistics_css.css',
                        ),
                    ),
                    'fullcalendar_css' => array(
                        'assets' => array(
                            'logistics/fullcalendar/fullcalendar.css',
                        ),
                    ),
                    'logistics_js' => array(
                        'assets' => array(
                            'logistics/js/logistics.js',
                            'logistics/fullcalendar/fullcalendar.js',
                        ),
                    ),
                    'minicolor_css' => array(
                        'assets' => array(
                            'logistics/minicolor/jquery.miniColors.css',
                        ),
                    ),
                    'minicolor_js' => array(
                        'assets' => array(
                            'logistics/minicolor/jquery.miniColors.min.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'logistics_install'               => 'LogisticsBundle\Controller\Admin\InstallController',
            'logistics_admin_driver'          => 'LogisticsBundle\Controller\Admin\DriverController',
            'logistics_admin_van_reservation' => 'LogisticsBundle\Controller\Admin\VanReservationController',

            'logistics_index'                 => 'LogisticsBundle\Controller\IndexController',
            'logistics_auth'                  => 'LogisticsBundle\Controller\AuthController',
            'logistics_lease'                 => 'LogisticsBundle\Controller\LeaseController',
        ),
    ),
);
