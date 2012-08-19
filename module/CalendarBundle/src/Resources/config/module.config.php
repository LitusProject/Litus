<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'calendar_install' => 'CalendarBundle\Controller\Admin\InstallController',
                'admin_calendar'   => 'CalendarBundle\Controller\Admin\CalendarController',

                'common_calendar'  => 'CalendarBundle\Controller\CalendarController',
            ),

            'assetic_configuration' => array(
                'parameters' => array(
                    'config' => array(
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
                                                'name' => 'LessFilter',
                                                'parameters' => array(
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
                ),
            ),

            'doctrine_config' => array(
                'parameters' => array(
                    'entityPaths' => array(
                        'calendarbundle' => __DIR__ . '/../../Entity',
                    ),
                ),
            ),

            'translator' => array(
                'parameters' => array(
                    'adapter' => 'ArrayAdapter',
                    'translations' => array(
                        'calendar_en' => array(
                            'content' => __DIR__ . '/../translations/common.en.php',
                            'locale'  => 'en',
                        ),
                        'calendar_nl' => array(
                            'content' => __DIR__ . '/../translations/common.nl.php',
                            'locale'  => 'nl',
                        ),
                    ),
                ),
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'calendar_views' => __DIR__ . '/../views',
                    ),
                ),
            ),

            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'calendar_install' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/admin/install/calendar',
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
                                'route'    => '/admin/content/calendar[/:action[/:id]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'      => '[a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'admin_calendar',
                                    'action'     => 'manage',
                                ),
                            ),
                        ),
                        'common_calendar' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/calendar[/:action[/:id]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'name'    => '[a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'common_calendar',
                                    'action'     => 'overview',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
