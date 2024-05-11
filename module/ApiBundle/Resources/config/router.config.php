<?php

use ApiBundle\Controller\BurgieclanController;

return array(
    'routes' => array(
        'api_admin_key' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/api/key[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'api_admin_key',
                    'action'     => 'manage',
                ),
            ),
        ),
        'api_auth' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/auth[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_auth',
                ),
            ),
        ),
        'api_br' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/br[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_br',
                ),
            ),
        ),
        'api_burgieclan' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/api/burgieclan[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'api_burgieclan',
                ),
            ),
        ),
        'api_calendar' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/calendar[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_calendar',
                ),
            ),
        ),
        'api_commu' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/commu[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_commu',
                ),
            ),
        ),
        'api_config' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/config[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_config',
                ),
            ),
        ),
        'api_cudi' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/cudi[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_cudi',
                ),
            ),
        ),
        'api_door' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/door[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_door',
                ),
            ),
        ),
        'api_fak' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/fak[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_fak',
                ),
            ),
        ),
        'api_mail' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/mail[/:action[/type/:type]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                    'type'     => '(tar|zip)',
                ),
                'defaults'    => array(
                    'controller' => 'api_mail',
                ),
            ),
        ),
        'api_member' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/members[/:action][/]',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'api_member',
                    'action'     => 'all',
                ),
            ),
        ),
        'api_news' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/news[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_news',
                ),
            ),
        ),
        'api_oauth' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/oauth[/:action[/identification/:identification[/hash/:hash]]][/]',
                'constraints' => array(
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                    'language'       => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_oauth',
                    'action'     => 'authorize',
                ),
            ),
        ),
        'api_shift' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/api/shift[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'api_shift',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'api_admin_key' => 'ApiBundle\Controller\Admin\KeyController',

        'api_auth'      => 'ApiBundle\Controller\AuthController',
        'api_calendar'  => 'ApiBundle\Controller\CalendarController',
        'api_config'    => 'ApiBundle\Controller\ConfigController',
        'api_br'        => 'ApiBundle\Controller\BrController',
        'api_burgieclan' => BurgieclanController::class,
        'api_commu'     => 'ApiBundle\Controller\CommuController',
        'api_cudi'      => 'ApiBundle\Controller\CudiController',
        'api_door'      => 'ApiBundle\Controller\DoorController',
        'api_fak'       => 'ApiBundle\Controller\FakController',
        'api_mail'      => 'ApiBundle\Controller\MailController',
        'api_member'    => 'ApiBundle\Controller\MemberController',
        'api_news'      => 'ApiBundle\Controller\NewsController',
        'api_oauth'     => 'ApiBundle\Controller\OAuthController',
        'api_shift'     => 'ApiBundle\Controller\ShiftController',
    ),
);
