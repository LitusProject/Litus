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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'routes' => array(
        'logistics_admin_driver' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/driver[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_driver',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_article' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/article[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_article',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_article_typeahead' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/article/typeahead[/:string][/]',
                'constraints' => array(
                    'string'       => '[%a-zA-Z0-9:.,_\-\(\)]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_article',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'logistics_admin_order' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/order[/:action[/:id][/:map][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'map'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_order',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_request' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/request[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_request',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_van_reservation' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/reservation/van[/:action[/:id][/page/:page][/return/:return]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'return' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_van_reservation',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_piano_reservation' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/reservation/piano[/:action[/:id][/page/:page][/return/:return]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'return' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_admin_piano_reservation',
                    'action'     => 'manage',
                ),
            ),
        ),
        'logistics_admin_lease' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/logistics/leases[/:action[/:id]][/page/:page][/]',
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
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/logistics[/:action][/date/:date][/:id][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
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
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/logistics/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
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
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/logistics/export[/:token]/ical.ics',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'token'    => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_index',
                    'action'     => 'export',
                ),
            ),
        ),
        'logistics_reservation_fetch' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/logistics/fetch[/:start][/:end][/]',
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
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/reservations/piano[/:action][/date/:date][/:id][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
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
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/logistics/lease[/:action[/:id]][/page/:page][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'language' => '(en|nl)',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_lease',
                    'action'     => 'index',
                ),
            ),
        ),
        'logistics_catalog' => array(
            'type' => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/logistics/catalog[/:action[/:order][/request/:request][/page/:page]][/]',
                'constraints' => array(
                    'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'order'     => '[0-9]*',
                    'request'     => '[0-9]*',
                    'language' => '(en|nl)',
                    'page'      => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_catalog',
                    'action' => 'overview',
                ),
            ),
        ),
        'logistics_catalog_typeahead' => array(
            'type' => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/logistics/catalog[/:order]/typeahead[/:string][/]',
                'constraints' => array(
                    'order'     => '[0-9]*',
                    'language' => '(en|nl)',
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'logistics_catalog',
                    'action' => 'search',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'logistics_admin_driver'            => 'LogisticsBundle\Controller\Admin\DriverController',
        'logistics_admin_article'           => 'LogisticsBundle\Controller\Admin\ArticleController',
        'logistics_admin_order'             => 'LogisticsBundle\Controller\Admin\OrderController',
        'logistics_admin_request'           => 'LogisticsBundle\Controller\Admin\RequestController',
        'logistics_admin_van_reservation'   => 'LogisticsBundle\Controller\Admin\VanReservationController',
        'logistics_admin_piano_reservation' => 'LogisticsBundle\Controller\Admin\PianoReservationController',
        'logistics_admin_lease'             => 'LogisticsBundle\Controller\Admin\LeaseController',

        'logistics_index' => 'LogisticsBundle\Controller\IndexController',
        'logistics_auth'  => 'LogisticsBundle\Controller\AuthController',
        'logistics_piano' => 'LogisticsBundle\Controller\PianoController',
        'logistics_lease' => 'LogisticsBundle\Controller\LeaseController',
        'logistics_catalog' => 'LogisticsBundle\Controller\CatalogController',
    ),
);
