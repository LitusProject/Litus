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
            'admin_shift' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/shift[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_shift',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_shift_counter' => array(
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
                        'controller' => 'admin_shift_counter',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_shift_ranking' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/shift/ranking[/:action][/:academicyear][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_shift_ranking',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_subscription' => array(
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
                        'controller' => 'admin_subscription',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_unit' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/unit[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_unit',
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
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/shift.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/shift.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'shift_install'       => 'ShiftBundle\Controller\Admin\InstallController',
            'admin_shift'         => 'ShiftBundle\Controller\Admin\ShiftController',
            'admin_shift_counter' => 'ShiftBundle\Controller\Admin\CounterController',
            'admin_shift_ranking' => 'ShiftBundle\Controller\Admin\RankingController',
            'admin_subscription'  => 'ShiftBundle\Controller\Admin\SubscriptionController',
            'admin_unit'          => 'ShiftBundle\Controller\Admin\UnitController',

            'shift'               => 'ShiftBundle\Controller\ShiftController',
        ),
    ),
);
