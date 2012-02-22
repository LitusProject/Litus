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
    'display_exceptions'    => true,
    'encoding'				=> 'ISO-8859-1',
    'di'                    => array(
        'instance' => array(
            'alias'                          => array(
                'authentication'                 => 'CommonBundle\Component\Authentication\Authentication',
                'authentication_doctrineadapter' => 'CommonBundle\Component\Authentication\Adapter\Doctrine',
                'authentication_doctrineservice' => 'CommonBundle\Component\Authentication\Service\Doctrine',
                'authentication_sessionstorage'  => 'Zend\Authentication\Storage\Session',
                                
                'admin_auth'                     => 'CommonBundle\Controller\Admin\AuthController',
                'admin_dashboard'                => 'CommonBundle\Controller\Admin\DashboardController',
                'admin_role'                     => 'CommonBundle\Controller\Admin\RoleController',
                'admin_user'                     => 'CommonBundle\Controller\Admin\UserController',
            ),
            'assetic_configuration'          => array(
                'parameters' => array(
                    'config' => array(
                        'cacheEnabled' => true,
                        'cachePath'    => __DIR__ . '/../../../../../data/cache',
                        'webPath'      => __DIR__ . '/../../../../../public/_assetic',
                        'baseUrl'      => '/_assetic',
                        'controllers'  => $asseticConfig['controllers'],
                        'routes'       => $asseticConfig['routes'],
                        'modules'      => array(
                            'commonbundle' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                	'common_jquery' => array(
                                	    'assets'  => array(
                                	        'common/js/jquery-1.7.1.min.js',
                                	    ),
                                	),
                                	'common_jqueryui' => array(
                                	    'assets'  => array(
                                	        'common/js/jqueryui-1.8.16.min.js',
                                	    ),
                                	),
                                    
                                    'admin_css' => array(
                                    	'assets' => array(
                                    		'admin/less/admin.less',
                                    	),
                                    	'filters' => array(
                                    		'admin_less' => array(
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
                                            'output' => 'admin_css.css',
                                        ),
                                    ),
                                    'admin_js' => array(
                                        'assets'  => array(
                                            'admin/js/*.js',
                                        ),
                                    ),
                                    
                                    'bootstrap_css' => array(
                                    	'assets' => array(
                                    		'bootstrap/less/bootstrap.less',
                                    	),
                                    	'filters' => array(
                                    		'bootstrap_less' => array(
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
                                            'output' => 'bootstrap_css.css',
                                        ),
                                    ),
                                    'bootstrap_js_alert' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-alert.js'
                                        ),
                                    ),
                                    'bootstrap_js_button' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-button.js'
                                        ),
                                    ),
                                    'bootstrap_js_carousel' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-carousel.js'
                                        ),
                                    ),
                                    'bootstrap_js_collapse' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-collapse.js'
                                        ),
                                    ),
                                    'bootstrap_js_dropdown' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-dropdown.js'
                                        ),
                                    ),
                                    'bootstrap_js_modal' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-modal.js'
                                        ),
                                    ),
                                    'bootstrap_js_popover' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-popover.js'
                                        ),
                                    ),
                                    'bootstrap_js_scrollspy' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-scrollspy.js'
                                        ),
                                    ),
                                    'bootstrap_js_tab' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-tab.js'
                                        ),
                                    ),
                                    'bootstrap_js_tooltip' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-tooltip.js'
                                        ),
                                    ),
                                    'bootstrap_js_transition' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-transition.js'
                                        ),
                                    ),
                                    'bootstrap_js_typeahead' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-typeahead.js'
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
                       
            'authentication'                 => array(
            	'parameters' => array(
            		'adapter' => 'authentication_doctrineadapter',
            		'service' => 'authentication_doctrineservice',
            	),
            ),
            'authentication_doctrineadapter' => array(
            	'parameters' => array(
            		'entityManager'  => 'doctrine_em',
            		'entityName'     => '"CommonBundle\Entity\Users\Person"',
            		'identityColumn' => 'username',
            	),
            ),
            'authentication_doctrineservice' => array(
            	'parameters' => array(
            		'entityManager' => 'doctrine_em',
            		'entityName'    => '"CommonBundle\Entity\Users\Session"',
            		'expire'        => 2678400,
            		'storage'       => 'authentication_sessionstorage',
            		'namespace'     => 'Litus_Auth',
            		'cookieSuffix'  => 'Session',
            	),
            ),
            'authentication_sessionstorage' => array(
            	'parameters' => array(
            		'namespace' => 'Litus_Auth',
            		'member'    => 'storage',
            	),
            ),
            
            'doctrine_config' => array(
                'parameters' => array(
                	'entityPaths' => array(
                		'commonbundle' => __DIR__ . '/../../Entity',
                	),
                ),
            ),
            
            'ZfTwig\TwigEnvironment' => array(
            	'parameters' => array(
            		'options' => array(
            			'charset' => 'ISO-8859-1',
            		),
            	),
            ),
        ),
    ),
    'routes' => array(
    	'admin_dashboard' => array(
    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
    	    'options' => array(
    	        'route'    => '/admin[/dashboard]',
    	        'defaults' => array(
    	            'controller' => 'admin_dashboard',
    	            'action'     => 'index',
    	        ),
    	    ),
    	),
    	'admin_auth' => array(
    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
    	    'options' => array(
    	        'route'    => '/admin/auth[/:action]',
    	        'constraints' => array(
    	        	'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
    	        ),
    	        'defaults' => array(
    	            'controller' => 'admin_auth',
    	            'action'     => 'login',
    	        ),
    	    ),
    	),
    	'admin_role' => array(
    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
    	    'options' => array(
    	        'route'    => '/admin/role[/:action[/:page][/:name[/:confirm]]]',
    	        'constraints' => array(
    	        	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
    	        	'page'	  => '[0-9]*',
    	        	'id'      => '[0-9]*',
    	        	'confirm' => '[01]',
    	        ),
    	        'defaults' => array(
    	            'controller' => 'admin_role',
    	            'action'     => 'manage',
    	        ),
    	    ),
    	),
    	'admin_user' => array(
    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
    	    'options' => array(
    	        'route'    => '/admin/user[/:action[/:page][/:id[/:confirm]]]',
    	        'constraints' => array(
    	        	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
    	        	'page'	  => '[0-9]*',
    	        	'id'      => '[0-9]*',
    	        	'confirm' => '[01]',
    	        ),
    	        'defaults' => array(
    	            'controller' => 'admin_user',
    	            'action'     => 'manage',
    	        ),
    	    ),
    	),
	),
);