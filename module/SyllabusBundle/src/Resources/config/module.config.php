<?php
return array(
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'syllabus_update' => 'SyllabusBundle\Controller\Admin\UpdateController',
            ),
            'doctrine_config' => array(
                'parameters' => array(
                	'entityPaths' => array(
                		'syllabusbundle' => __DIR__ . '/../../Entity',
                	),
                ),
            ), 
        ),
    ),
    'routes' => array(
        'syllabus_update' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus[/:action[/:id]]',
        		'constraints' => array(
        			'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'      => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'syllabus_update',
        			'action'     => 'update',
        		),
        	),
        ),
    ),
);