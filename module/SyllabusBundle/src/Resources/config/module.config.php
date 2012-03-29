<?php
return array(
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'syllabus_install'       => 'SyllabusBundle\Controller\Admin\InstallerController',
                'admin_update_syllabus'  => 'SyllabusBundle\Controller\Admin\UpdateController',
                'admin_study'            => 'SyllabusBundle\Controller\Admin\StudyController',
                'admin_subject'          => 'SyllabusBundle\Controller\Admin\SubjectController',
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
        			'action'     => 'update',
        		),
        	),
        ),
        'admin_study' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/study[/:action[/:id]]',
        		'constraints' => array(
        			'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'      => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_study',
        			'action'     => 'manage',
        		),
        	),
        ),
        'admin_study_search' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        	'route' => '/admin/syllabus/study/search[/:field/:string]',
        		'constraints' => array(
        			'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_study',
        			'action'     => 'search',
        		),
        	),
        ),
        'admin_study_paginator' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/syllabus/study/manage[/:page]',
        		'constraints' => array(
        			'page'      => '[0-9]*',
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
        		'route' => '/admin/syllabus/subject[/:action[/:id]]',
        		'constraints' => array(
        			'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id'      => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_subject',
        			'action'     => 'manage',
        		),
        	),
        ),
        'admin_subject_search' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        	'route' => '/admin/syllabus/subject/:id/search[/:field/:string]',
        		'constraints' => array(
        		    'id'     => '[0-9]*',
        			'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_subject',
        			'action'     => 'search',
        		),
        	),
        ),
    ),
);