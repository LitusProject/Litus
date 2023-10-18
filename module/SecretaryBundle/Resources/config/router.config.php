<?php

return array(
    'routes' => array(
        'secretary_admin_registration' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/secretary/registration[/:action[/:id][/organization/:organization]][/:academicyear][/:field/:string][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'organization' => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'secretary_admin_registration',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_admin_export' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/secretary/export[/:action[/:id][/organization/:organization]][/:academicyear][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'organization' => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                ),
                'defaults'    => array(
                    'controller' => 'secretary_admin_export',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_admin_photos' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/secretary/photos[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'secretary_admin_photos',
                    'action'     => 'photos',
                ),
            ),
        ),
        'secretary_admin_promotion' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/secretary/promotion[/:action[/:id][/page/:page]][/:academicyear][/:field/:string][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'page'         => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'secretary_admin_promotion',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_registration' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/secretary/registration[/:action][/identification/:identification[/hash/:hash]][/]',
                'constraints' => array(
                    'language'       => '(en|nl)',
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'secretary_registration',
                    'action'     => 'add',
                ),
            ),
        ),
        'secretary_pull' => array(
            'type' => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/secretary/pull[/:action[/:id[/code/:code]]][/]',
                'constraints' => array(
                    'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language'  => '(en|nl)',
                    'id'        => '[0-9]*',
                    'code'      => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'secretary_pull',
                    'action'     => 'view',
                ),
            ),
        ),
        'secretary_admin_working_group' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/secretary/workinggroup[/:action[/:id][/:field/:string][/page/:page]][/:academicyear][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'secretary_admin_working_group',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_admin_pull' => array(
            'type' => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/secretary/pull[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id' => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'secretary_admin_pull',
                    'action' => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'secretary_admin_registration'  => 'SecretaryBundle\Controller\Admin\RegistrationController',
        'secretary_admin_export'        => 'SecretaryBundle\Controller\Admin\ExportController',
        'secretary_admin_promotion'     => 'SecretaryBundle\Controller\Admin\PromotionController',
        'secretary_admin_photos'        => 'SecretaryBundle\Controller\Admin\PhotosController',
        'secretary_registration'        => 'SecretaryBundle\Controller\RegistrationController',
        'secretary_admin_working_group' => 'SecretaryBundle\Controller\Admin\WorkingGroupController',
        'secretary_admin_pull'          => 'SecretaryBundle\Controller\Admin\PullController',
        'secretary_pull'                => 'SecretaryBundle\Controller\PullController',
    ),
);
