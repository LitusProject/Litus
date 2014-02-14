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
        'calendar_install' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/install/calendar[/]',
                'constraints' => array(
                ),
                'defaults' => array(
                    'controller' => 'calendar_install',
                    'action'     => 'index',
                ),
            ),
        ),
        'calendar_admin_calendar' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/site/calendar[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'calendar_admin_calendar',
                    'action'     => 'manage',
                ),
            ),
        ),
        'calendar_admin_calendar_registration' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/site/calendar/registration[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'calendar_admin_calendar_registration',
                    'action'     => 'manage',
                ),
            ),
        ),
        'calendar' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/calendar[/:action[/:name]][/]',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'name'     => '[a-zA-Z0-9\-_]*',
                ),
                'defaults' => array(
                    'controller' => 'calendar',
                    'action'     => 'overview',
                ),
            ),
        ),
        'calendar_export' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/calendar/export/ical.ics',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'calendar',
                    'action'     => 'export',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'calendar_install'                     => 'CalendarBundle\Controller\Admin\InstallController',
        'calendar_admin_calendar'              => 'CalendarBundle\Controller\Admin\CalendarController',
        'calendar_admin_calendar_registration' => 'CalendarBundle\Controller\Admin\RegistrationController',

        'calendar'                             => 'CalendarBundle\Controller\CalendarController',
    ),
);
