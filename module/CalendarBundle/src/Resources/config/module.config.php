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
                'calendar_install'  => 'CalendarBundle\Controller\Admin\InstallController',
                'admin_calendar'    => 'CalendarBundle\Controller\Admin\CalendarController',
            ),

            'doctrine_config' => array(
                'parameters' => array(
                    'entityPaths' => array(
                        'calendarbundle' => __DIR__ . '/../../Entity',
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
                    ),
                ),
            ),
        ),
    ),
);
