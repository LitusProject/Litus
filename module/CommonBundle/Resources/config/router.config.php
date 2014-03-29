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
        'common_admin_academic' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/academic[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_academic',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_admin_academic_typeahead' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/academic/typeahead[/:string][/]',
                'constraints' => array(
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_academic',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'common_admin_person_typeahead' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/person/typeahead[/:string][/]',
                'constraints' => array(
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_person',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'common_admin_auth' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                'constraints' => array(
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_auth',
                    'action'     => 'login',
                ),
            ),
        ),
        'common_admin_cache' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cache[/:action[/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_cache',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_admin_config' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/config[/:action[/key/:key]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'key'    => '[a-zA-Z][\.a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_config',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_admin_index' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin[/]',
                'defaults' => array(
                    'controller' => 'common_admin_index',
                    'action'     => 'index',
                ),
            ),
        ),
        'common_admin_location' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/location[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_location',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_admin_role' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/role[/:action[/name/:name[/:id]][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'name'   => '[a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_role',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_admin_session' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/session/:action[/:id][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-z0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_session',
                    'action'     => 'index',
                ),
            ),
        ),
        'common_admin_unit' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/unit[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'common_admin_unit',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_index' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language][/]',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'common_index',
                    'action'     => 'index',
                ),
            ),
        ),
        'common_account' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/account[/:action[/code/:code][/image/:image][/return/:return]][/]',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                    'code'     => '[a-zA-Z0-9_-]*',
                    'image'    => '[a-zA-Z0-9]*',
                    'return'   => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_account',
                    'action'     => 'index',
                ),
            ),
        ),
        'common_session' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/session[/:action[/:id]][/]',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_session',
                    'action'     => 'manage',
                ),
            ),
        ),
        'common_auth' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                'constraints' => array(
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                    'language'       => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'common_auth',
                    'action'     => 'login',
                ),
            ),
        ),
        'common_robots' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/robots.txt',
                'constraints' => array(
                ),
                'defaults' => array(
                    'controller' => 'common_robots',
                    'action'     => 'index',
                ),
            ),
        ),
        'common_praesidium' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/praesidium[/:action[/:academicyear]][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                ),
                'defaults' => array(
                    'controller' => 'common_praesidium',
                    'action'     => 'overview',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'common_admin_academic' => 'CommonBundle\Controller\Admin\AcademicController',
        'common_admin_auth'     => 'CommonBundle\Controller\Admin\AuthController',
        'common_admin_config'   => 'CommonBundle\Controller\Admin\ConfigController',
        'common_admin_cache'    => 'CommonBundle\Controller\Admin\CacheController',
        'common_admin_index'    => 'CommonBundle\Controller\Admin\IndexController',
        'common_admin_location' => 'CommonBundle\Controller\Admin\LocationController',
        'common_admin_person'   => 'CommonBundle\Controller\Admin\PersonController',
        'common_admin_role'     => 'CommonBundle\Controller\Admin\RoleController',
        'common_admin_session'  => 'CommonBundle\Controller\Admin\SessionController',
        'common_admin_unit'     => 'CommonBundle\Controller\Admin\UnitController',

        'common_index'          => 'CommonBundle\Controller\IndexController',
        'common_account'        => 'CommonBundle\Controller\AccountController',
        'common_session'        => 'CommonBundle\Controller\SessionController',
        'common_auth'           => 'CommonBundle\Controller\AuthController',
        'common_robots'         => 'CommonBundle\Controller\RobotsController',
        'common_praesidium'     => 'CommonBundle\Controller\PraesidiumController',
    ),
);
