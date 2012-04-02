<?php
return array(
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'prof_install'           => 'ProfBundle\Controller\Admin\InstallerController',
                'prof'      	         => 'ProfBundle\Controller\Prof\IndexController',
                'prof_auth'      	     => 'ProfBundle\Controller\Prof\AuthController',
                'prof_article'           => 'ProfBundle\Controller\Prof\ArticleController',
                'prof_file'              => 'ProfBundle\Controller\Prof\FileController',
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
        'prof' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof[/:action]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'session' => '[0-9]*',
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
        			'session' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_auth',
        			'action'     => 'login',
        		),
        	),
        ),
        'prof_article' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/article[/:action]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'session' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_article',
        			'action'     => 'manage',
        		),
        	),
        ),
        'prof_file' => array(
        	'type'    => 'Zend\Mvc\Router\Http\Segment',
        	'options' => array(
        		'route' => '/prof/files[/:action]',
        		'constraints' => array(
        			'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        			'session' => '[0-9]*',
        		),
        		'defaults' => array(
        			'controller' => 'prof_file',
        			'action'     => 'manage',
        		),
        	),
        ),
    ),
);