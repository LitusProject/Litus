<?php

return array(
    'routes' => array(
        'calendar_admin_calendar' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/calendar[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'calendar_admin_calendar',
                    'action'     => 'manage',
                ),
            ),
        ),
        'calendar_admin_calendar_registration' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/calendar/registration[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'calendar_admin_calendar_registration',
                    'action'     => 'manage',
                ),
            ),
        ),
        'calendar' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/calendar[/:action[/:name]][/]',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'name'     => '[a-zA-Z0-9\-_]*',
                ),
                'defaults'    => array(
                    'controller' => 'calendar',
                    'action'     => 'overview',
                ),
            ),
        ),
        'calendar_export' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/calendar/export/ical.ics',
                'constraints' => array(
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'calendar',
                    'action'     => 'export',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'calendar_admin_calendar'              => 'CalendarBundle\Controller\Admin\CalendarController',
        'calendar_admin_calendar_registration' => 'CalendarBundle\Controller\Admin\RegistrationController',

        'calendar'                             => 'CalendarBundle\Controller\CalendarController',
    ),
);
