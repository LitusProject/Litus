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
        'api_admin_key' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/api/key[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'api_admin_key',
                    'action'     => 'manage',
                ),
            ),
        ),
        'api_auth' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/api/auth[/:action][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'api_auth',
                ),
            ),
        ),
        'api_calendar' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/api/calendar[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'api_calendar',
                ),
            ),
        ),
        'api_cudi' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/api/cudi[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'api_cudi',
                ),
            ),
        ),
        'api_door' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/api/door[/:action][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'api_door',
                ),
            ),
        ),
        'api_mail' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/api/mail[/:action[/type/:type]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'type'   => '(tar|zip)'
                ),
                'defaults' => array(
                    'controller' => 'api_mail',
                ),
            ),
        ),
        'api_news' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/api/news[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'api_news',
                ),
            ),
        ),
        'api_oauth' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/api/oauth[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'api_oauth',
                ),
            ),
        ),
        'api_shift' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/api/shift[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'api_shift',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'api_admin_key' => 'ApiBundle\Controller\Admin\KeyController',

        'api_auth'      => 'ApiBundle\Controller\AuthController',
        'api_calendar'  => 'ApiBundle\Controller\CalendarController',
        'api_cudi'      => 'ApiBundle\Controller\CudiController',
        'api_door'      => 'ApiBundle\Controller\DoorController',
        'api_mail'      => 'ApiBundle\Controller\MailController',
        'api_news'      => 'ApiBundle\Controller\NewsController',
        'api_oauth'     => 'ApiBundle\Controller\OAuthController',
        'api_shift'     => 'ApiBundle\Controller\ShiftController',
    ),
);
