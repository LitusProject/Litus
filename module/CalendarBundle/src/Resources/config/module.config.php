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
 
$asseticConfig = include __DIR__ . '/../../../../../config/assetic.config.php';

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'install_calendar'  => 'CalendarBundle\Controller\Admin\InstallController',
                'admin_calendar'    => 'CalendarBundle\Controller\Admin\CalendarController',

                'common_calendar'   => 'CalendarBundle\Controller\CalendarController',
            ),
            'assetic_configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'modules'      => array(
                            'calendarbundle' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                	'calendar' => array(
                                	    'assets'  => array(
                                	        'css/calendar.css',
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
        				'calendar_admin_en' => array(
                			'content' => __DIR__ . '/../translations/admin.en.php',
                			'locale' => 'en',
                		),
                		'calendar_admin_nl' => array(
                			'content' => __DIR__ . '/../translations/admin.nl.php',
                			'locale' => 'nl',
                		),
            		),
            	),
            ),
        ),
    ),
    'routes' => array(
        'install_calendar' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/install/calendar',
                'constraints' => array(
                ),
                'defaults' => array(
                    'controller' => 'install_calendar',
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
                	'id'      => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'admin_calendar',
                    'action'     => 'manage',
                ),
            ),
        ),
        /*'common_calendar' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '[/:language]/calendar[/:action[/:name]]',
                'constraints' => array(
                	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                	'name'  => '[a-zA-Z0-9_-]*',
                    'language' => '[a-zA-Z][a-zA-Z_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_calendar',
                    'action'     => 'overview',
                ),
            ),
        ),*/
	),
);
