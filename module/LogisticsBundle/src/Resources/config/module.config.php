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
			'logistics_test' => array(
				'type'    => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/admin/logistics/test',
					'defaults' => array(
						'controller' => 'logistics_temp',
						'action'     => 'index',
					),
				),
			),
		)
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'logistics_view' => __DIR__ . '/../views',
		),
	),
	'controllers' => array(
		'invokables' => array(
			'logistics_temp'                 => 'LogisticsBundle\Controller\TestController',
		),
	),
);