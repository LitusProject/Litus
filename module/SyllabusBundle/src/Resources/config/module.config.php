<?php
return array(
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'syllabus_install'       => 'SyllabusBundle\Controller\Admin\InstallController',
                
                'admin_update_syllabus'  => 'SyllabusBundle\Controller\Admin\UpdateController',
                'admin_study'            => 'SyllabusBundle\Controller\Admin\StudyController',
                'admin_subject'          => 'SyllabusBundle\Controller\Admin\SubjectController',
                'admin_prof'             => 'SyllabusBundle\Controller\Admin\ProfController',
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
        'syllabus_install' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/install/syllabus',
        		'defaults' => array(
        			'controller' => 'syllabus_install',
        			'action'     => 'index',
        		),
        	),
        ),
        'admin_update_syllabus' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/update[/:action[/:id]]',
        		'constraints' => array(
        			'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'      => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_update_syllabus',
        			'action'     => 'index',
        		),
        	),
        ),
        'admin_study' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/study[/:action[/:id]][/:academicyear][/page/:page][/:field/:string]',
        		'constraints' => array(
        			'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'           => '[0-9]*',
        			'academicyear' => '[0-9]{4}-[0-9]{4}',
    				'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
    				'string'       => '[a-zA-Z][%a-zA-Z0-9_-]*',
					'page'         => '[0-9]+',
        		),
        		'defaults' => array(
        			'controller' => 'admin_study',
        			'action'     => 'manage',
        		),
        	),
        ),
        'admin_subject' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/subject[/:action[/:id][/:academicyear][/:field/:string]]',
        		'constraints' => array(
        			'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'           => '[0-9]*',
        			'academicyear' => '[0-9]{4}-[0-9]{4}',
        			'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'string'       => '[a-zA-Z][%a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_subject',
        			'action'     => 'manage',
        		),
        	),
        ),
        'admin_subject_typeahead' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/subject/typeahead/:academicyear[/:string]',
        		'constraints' => array(
        			'academicyear' => '[0-9]{4}-[0-9]{4}',
        			'string'       => '[a-zA-Z][a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_subject',
        			'action'     => 'typeahead',
        		),
        	),
        ),
        'admin_prof' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/prof[/:action[/:id]][/:academicyear]',
        		'constraints' => array(
        			'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'           => '[0-9]*',
        			'academicyear' => '[0-9]{4}-[0-9]{4}',
        		),
        		'defaults' => array(
        			'controller' => 'admin_prof',
        			'action'     => 'manage',
        		),
        	),
        ),
        'admin_prof_typeahead' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/prof/typeahead[/:string]',
        		'constraints' => array(
        			'string'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_prof',
        			'action'     => 'typeahead',
        		),
        	),
        ),
    ),
);