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
                'route' => '/admin/reservation/van[/:action[/:id][/page/:page][/return/:return]][/]',
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
        'logistics_admin_piano_reservation' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/reservation/piano[/:action[/:id][/page/:page][/return/:return]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'return'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_piano_reservation',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_lease' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/lease[/:action[/:id]][/page/:page][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_lease',
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
        'logistics_export' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/logistics/export/ical.ics',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'logistics_index',
                    'action'     => 'export',
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
        'logistics_piano' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/reservations/piano[/:action][/date/:date][/:id][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                    'date'     => '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}',
                    'id'       => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_piano',
                    'action'     => 'index',
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
    ),

    'controllers' => array(
        'logistics_install'                 => 'LogisticsBundle\Controller\Admin\InstallController',
        'logistics_admin_driver'            => 'LogisticsBundle\Controller\Admin\DriverController',
        'logistics_admin_van_reservation'   => 'LogisticsBundle\Controller\Admin\VanReservationController',
        'logistics_admin_piano_reservation' => 'LogisticsBundle\Controller\Admin\PianoReservationController',
        'logistics_admin_lease'             => 'LogisticsBundle\Controller\Admin\LeaseController',

        'logistics_index'                   => 'LogisticsBundle\Controller\IndexController',
        'logistics_auth'                    => 'LogisticsBundle\Controller\AuthController',
        'logistics_piano'                   => 'LogisticsBundle\Controller\PianoController',
        'logistics_lease'                   => 'LogisticsBundle\Controller\LeaseController',
    ),
);
