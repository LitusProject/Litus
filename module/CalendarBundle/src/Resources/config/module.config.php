<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'router' => array(
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
            'admin_calendar' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/site/calendar[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_calendar',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_calendar_registration' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/site/calendar/registration[/:action][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_calendar_registration',
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
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/common.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/common.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'calendar_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'CalendarBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'calendarbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'calendar_install'            => 'CalendarBundle\Controller\Admin\InstallController',
            'admin_calendar'              => 'CalendarBundle\Controller\Admin\CalendarController',
            'admin_calendar_registration' => 'CalendarBundle\Controller\Admin\RegistrationController',

            'calendar'  => 'CalendarBundle\Controller\CalendarController',
        ),
    ),
    'assetic_configuration' => array(
        'modules'      => array(
            'calendarbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'calendar_css' => array(
                        'assets' => array(
                            'calendar/less/calendar.less'
                        ),
                        'filters' => array(
                            'calendar_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'calendar_css.css',
                        ),
                    ),
                    'calendar_js' => array(
                        'assets' => array(
                            'calendar/js/calendar.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
