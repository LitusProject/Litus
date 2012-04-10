<?php
return array(
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'prof_install'           => 'ProfBundle\Controller\Admin\InstallController',
                'admin_action'           => 'ProfBundle\Controller\Admin\ActionController',
                'prof'      	         => 'ProfBundle\Controller\Prof\IndexController',
                'prof_auth'      	     => 'ProfBundle\Controller\Prof\AuthController',
                'prof_article'           => 'ProfBundle\Controller\Prof\ArticleController',
                'prof_article_mapping'   => 'ProfBundle\Controller\Prof\ArticleMappingController',
                'prof_prof'              => 'ProfBundle\Controller\Prof\ProfController',
                'prof_subject'           => 'ProfBundle\Controller\Prof\SubjectController',
                'prof_file'              => 'ProfBundle\Controller\Prof\FileController',
                'prof_comment'           => 'ProfBundle\Controller\Prof\CommentController',
            ),
            'assetic_configuration'          => array(
                'parameters' => array(
                    'config' => array(
                        'modules'      => array(
                            'profbundle' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'prof_css' => array(
                                    	'assets' => array(
                                    		'prof/less/base.less',
                                    	),
                                    	'filters' => array(
                                    		'prof_less' => array(
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
                                            'output' => 'prof_css.css',
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
                		'profbundle' => __DIR__ . '/../../Entity',
                	),
                ),
            ), 
        ),
    ),
    'routes' => array(
        'prof_install' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/install/prof',
        		'defaults' => array(
        			'controller' => 'prof_install',
        			'action'     => 'index',
        		),
        	),
        ),
        'admin_action' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/admin/prof/actions[/:action[/:id]]',
        		'contraints' => array(
        		    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        		    'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'admin_action',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof[/:action]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof',
        			'action'     => 'index',
        		),
        	),
        ),
        'prof_paginator' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof[/:page]',
        		'constraints' => array(
        			'page' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof',
        			'action'     => 'index',
        		),
        	),
        ),
        'prof_auth' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/cudi/prof/auth[/:action]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_auth',
        			'action'     => 'login',
        		),
        	),
        ),
        'prof_subject' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/subject[/:action[/:id]]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_subject',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_article' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/article[/:action[/:id]]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_article',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_article_typeahead' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/article/typeahead[/:string]',
        		'constraints' => array(
        			'string' => '[a-zA-Z][a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_article',
        			'action'     => 'typeahead',
        		),
        	),
        ),
        'prof_article_mapping' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/article/mapping[/:action[/:id]]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_article_mapping',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_file' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/files[/:action[/:id]]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_file',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_comment' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/comments[/:action[/:id]]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_comment',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_prof' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/prof[/:action[/:id]]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'id' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_prof',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_typeahead' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/prof/typeahead[/:string]',
        		'constraints' => array(
        			'string' => '[a-zA-Z][a-zA-Z0-9_-]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_prof',
        			'action'     => 'typeahead',
        		),
        	),
        ),
    ),
);