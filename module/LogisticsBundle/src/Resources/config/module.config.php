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
		)
	),
    'view_manager' => array(
        'template_path_stack' => array(
            'driver_view' => __DIR__ . '/../views',
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
	'controllers' => array(
		'invokables' => array(
			'admin_driver'                 => 'LogisticsBundle\Controller\Admin\DriverController',
			'admin_van_reservation'         => 'LogisticsBundle\Controller\Admin\VanReservationController',
		),
	),
);