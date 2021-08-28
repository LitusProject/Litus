<?php

return array(
    'routes' => array(
        'prom_admin_bus' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/prom/bus[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_admin_bus',
                    'action'     => 'manage',
                ),
            ),
        ),
        'prom_admin_code' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/prom/code[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_admin_code',
                    'action'     => 'manage',
                ),
            ),
        ),
        'prom_admin_passenger' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/prom/passenger[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_admin_passenger',
                    'action'     => 'manage',
                ),
            ),
        ),
        'prom_registration_index' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/prom/registration[/:action[/:code]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'code'   => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_registration_index',
                    'action'     => 'registration',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'prom_admin_bus'       => 'PromBundle\Controller\Admin\BusController',
        'prom_admin_code'      => 'PromBundle\Controller\Admin\CodeController',
        'prom_admin_passenger' => 'PromBundle\Controller\Admin\PassengerController',

        'prom_registration_index' => 'PromBundle\Controller\Registration\IndexController',
    ),
);
