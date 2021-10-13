<?php

return array(
    'routes' => array(
        'shift_admin_shift' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_shift',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shift_admin_registration_shift' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift/registration-shift[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_registration_shift',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shift_admin_shift_counter' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift/counter[/:action[/:id[/:person[/:payed]]]][/:academicyear][/:field/:string][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'person'       => '[0-9]*',
                    'payed'        => '(true|false)',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string'       => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_shift_counter',
                    'action'     => 'index',
                ),
            ),
        ),
        'shift_admin_shift_ranking' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift/ranking[/:action][/:academicyear][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_shift_ranking',
                    'action'     => 'index',
                ),
            ),
        ),
        'shift_admin_shift_weekly_change' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift/weekly-change[/:action][/:academicyear][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_shift_weekly_change',
                    'action'     => 'index',
                ),
            ),
        ),
        'shift_admin_shift_subscription' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift/subscription[/:action[/:id][/type/:type][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'type'   => '[a-zA-Z]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_shift_subscription',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shift_admin_registration_shift_subscription' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shift/registration-subscription[/:action[/:shift][/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'shift'  => '[0-9]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_registration_shift_subscription',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shift' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/shift[/:action[/:id]][/]',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift',
                    'action'     => 'index',
                ),
            ),
        ),
        'registration_shift' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/registration-shift[/:action[/:id]][/]',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'registration_shift',
                    'action'     => 'index',
                ),
            ),
        ),
        'registration_shift_auth' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/registration-shift/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                'constraints' => array(
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                    'language'       => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'registration_s_sale_auth',
                    'action'     => 'login',
                ),
            ),
        ),
        'shift_export' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/shift/export/:token/ical.ics',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'token'    => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift',
                    'action'     => 'export',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'shift_admin_shift'                           => 'ShiftBundle\Controller\Admin\ShiftController',
        'shift_admin_registration_shift'              => 'ShiftBundle\Controller\Admin\RegistrationShiftController',
        'shift_admin_shift_counter'                   => 'ShiftBundle\Controller\Admin\CounterController',
        'shift_admin_shift_ranking'                   => 'ShiftBundle\Controller\Admin\RankingController',
        'shift_admin_shift_weekly_change'             => 'ShiftBundle\Controller\Admin\WeeklyChangeController',
        'shift_admin_shift_subscription'              => 'ShiftBundle\Controller\Admin\SubscriptionController',
        'shift_admin_registration_shift_subscription' => 'ShiftBundle\Controller\Admin\RegistrationSubscriptionController',

        'shift'                                       => 'ShiftBundle\Controller\ShiftController',
        'registration_shift'                          => 'ShiftBundle\Controller\RegistrationShiftController',
    ),
);
