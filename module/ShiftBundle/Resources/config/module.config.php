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
    'router' => array(
        'routes' => array(
            'shift_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/shift[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'shift_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'shift_admin_shift' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/shift[/:action[/:id][/:field/:string][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'shift_admin_shift',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'shift_admin_shift_counter' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/shift/counter[/:action[/:id[/:person[/:payed]]]][/:academicyear][/:field/:string][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'person'       => '[0-9]*',
                        'payed'        => '(true|false)',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'shift_admin_shift_counter',
                        'action'     => 'index',
                    ),
                ),
            ),
            'shift_admin_shift_ranking' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/shift/ranking[/:action][/:academicyear][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'shift_admin_shift_ranking',
                        'action'     => 'index',
                    ),
                ),
            ),
            'shift_admin_shift_subscription' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/shift/subscription[/:action[/:id][/type/:type][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'type'   => '[a-zA-Z]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'shift_admin_shift_subscription',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'shift' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/shift[/:action[/:id][]][/]',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'shift',
                        'action'     => 'index',
                    ),
                ),
            ),
            'shift_export' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/shift/export/:token/ical.ics',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                        'token'    => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'shift',
                        'action'     => 'export',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'shift_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'ShiftBundle\Document' => 'odm_annotation_driver'
                ),
            ),
            'odm_annotation_driver' => array(
                'paths' => array(
                    'shiftbundle' => __DIR__ . '/../../Document',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'ShiftBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'shiftbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../translations',
                'pattern'  => 'shift.%s.php',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'shift_install'                  => 'ShiftBundle\Controller\Admin\InstallController',
            'shift_admin_shift'              => 'ShiftBundle\Controller\Admin\ShiftController',
            'shift_admin_shift_counter'      => 'ShiftBundle\Controller\Admin\CounterController',
            'shift_admin_shift_ranking'      => 'ShiftBundle\Controller\Admin\RankingController',
            'shift_admin_shift_subscription' => 'ShiftBundle\Controller\Admin\SubscriptionController',

            'shift'                          => 'ShiftBundle\Controller\ShiftController',
        ),
    ),
);
