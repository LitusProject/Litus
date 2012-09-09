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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
	'router' => array(
		'routes' => array(
			'admin_driver' => array(
				'type'    => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
                    'route' => '/admin/driver[/:action[/:id][/page/:page][/:field/:string]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
					'defaults' => array(
						'controller' => 'admin_driver',
						'action'     => 'manage',
					),
				),
			),
			'admin_van_reservation' => array(
			    'type'    => 'Zend\Mvc\Router\Http\Segment',
			    'options' => array(
			        'route' => '/admin/van_reservation[/:action[/:id][/page/:page][/:field/:string]]',
			        'constraints' => array(
			            'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
			            'id'      => '[0-9]*',
			            'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
			            'string'  => '[%a-zA-Z0-9_-]*',
			            'page'    => '[0-9]*',
			        ),
			        'defaults' => array(
			            'controller' => 'admin_van_reservation',
			            'action'     => 'manage',
			        ),
			    ),
			),
			'logistics_index' => array(
			    'type' => 'Zend\Mvc\Router\Http\Segment',
			    'options' => array(
			        'route' => '[/:language]/logistics[/:action]',
			        'constraints' => array(
			            'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
			            'session'  => '[0-9]*',
			            'language' => '[a-zA-Z][a-zA-Z_-]*',
			        ),
			        'defaults' => array(
			            'controller' => 'logistics_index',
			            'action'     => 'index',
			        ),
			    ),
			),
			'logistics_auth' => array(
			    'type' => 'Zend\Mvc\Router\Http\Segment',
			    'options' => array(
			        'route' => '[/:language]/logistics/auth[/:action]',
			        'constraints' => array(
			            'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
			            'session'  => '[0-9]*',
			            'language' => '[a-zA-Z][a-zA-Z_-]*',
			        ),
			        'defaults' => array(
			            'controller' => 'logistics_auth',
			            'action'     => 'login',
			        ),
			    ),
			),
			'logistics_reservation_fetch' => array(
			    'type'    => 'Zend\Mvc\Router\Http\Segment',
			    'options' => array(
			        'route' => '/logistics/fetch[/:start][/:end]',
			        'constraints' => array(
			            'start'       => '[0-9]*',
			            'end'       => '[0-9]*',
			        ),
			        'defaults' => array(
			            'controller' => 'logistics_index',
			            'action'     => 'fetch',
			        ),
			    ),
			),
		)
	),
    'view_manager' => array(
        'template_path_stack' => array(
            'logistics_layouts' => __DIR__ . '/../layouts',
            'logistics_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'LogisticsBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'logisticsbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'logisticsbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'logistics_css' => array(
                        'assets' => array(
                            'logistics/less/base.less',
                        ),
                        'filters' => array(
                            'logistics_less' => array(
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
                            'output' => 'logistics_css.css',
                        ),
                    ),
                    'fullcalendar_css' => array(
                        'assets' => array(
                            'logistics/fullcalendar/fullcalendar.css',
                        ),
                    ),
                    'fullcalendar_js' => array(
                        'assets' => array(
                            'logistics/fullcalendar/fullcalendar.min.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
	'controllers' => array(
		'invokables' => array(
			'admin_driver'                 => 'LogisticsBundle\Controller\Admin\DriverController',
			'admin_van_reservation'        => 'LogisticsBundle\Controller\Admin\VanReservationController',
		    'logistics_index'              => 'LogisticsBundle\Controller\IndexController',
			'logistics_auth'               => 'LogisticsBundle\Controller\AuthController',
		),
	),
);
