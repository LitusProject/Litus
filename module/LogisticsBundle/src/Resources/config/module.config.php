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
 * @author Niels Avonds <niels.avonds@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
	'router' => array(
		'routes' => array(
			'admin_driver' => array(
				'type'    => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/admin/logistics/driver',
					'defaults' => array(
						'controller' => 'admin_driver',
						'action'     => 'manage',
					),
				),
			),
		)
	),
    'view_manager' => array(
        'template_path_stack' => array(
            'mail_view' => __DIR__ . '/../views',
        ),
    ),
	'controllers' => array(
		'invokables' => array(
			'admin_driver'                 => 'LogisticsBundle\Controller\Admin\DriverController',
		),
	),
);