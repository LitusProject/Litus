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
    'di' => array(
        'instance' => array(
            'alias' => array(
            	'common_install'                   => 'CommonBundle\Controller\Admin\InstallController',
            	
                'authentication'                   => 'CommonBundle\Component\Authentication\Authentication',
                'authentication_action'            => 'CommonBundle\Component\Authentication\Action\Doctrine',
                'authentication_credentialadapter' => 'CommonBundle\Component\Authentication\Adapter\Doctrine\Credential',
                'authentication_doctrineservice'   => 'CommonBundle\Component\Authentication\Service\Doctrine',
                'authentication_sessionstorage'    => 'Zend\Authentication\Storage\Session',
				
				'index'                            => 'CommonBundle\Controller\IndexController',
				'account'                          => 'CommonBundle\Controller\AccountController',
				'admin_academic'                   => 'CommonBundle\Controller\Admin\AcademicController',
                'admin_auth'                       => 'CommonBundle\Controller\Admin\AuthController',
                'admin_config'                     => 'CommonBundle\Controller\Admin\ConfigController',
                'admin_index'                      => 'CommonBundle\Controller\Admin\IndexController',
                'admin_role'                       => 'CommonBundle\Controller\Admin\RoleController',
                
            	'translator'                       => 'CommonBundle\Component\Localisation\Translator',

                'mail_transport'                   => 'Zend\Mail\Transport\Sendmail',
            ),
            'assetic_configuration' => array(
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
                                	        'common/js/jquery-1.7.2.min.js',
                                	    ),
                                	),
                                	'common_jqueryui' => array(
                                	    'assets'  => array(
                                	        'common/js/jqueryui-1.8.16.min.js',
                                	    ),
                                	),
                                	'common_jquery_form' => array(
                                	    'assets'  => array(
                                	        'common/js/jquery.form.js',
                                	    ),
                                	),
                                    'common_form_upload_progress' => array(
                                        'assets'  => array(
                                            'common/js/jquery.form.js',
                                            'common/js/formUploadProgress.js',
                                        ),
                                    ),
                                    'common_permanent_modal' => array(
                                        'assets'  => array(
                                            'common/js/permanentModal.js',
                                        ),
                                    ),
                                    'common_socket' => array(
                                        'assets'  => array(
                                            'common/js/socket.js',
                                        ),
                                    ),
                                    'common_download_file' => array(
                                        'assets'  => array(
                                            'common/js/downloadFile.js',
                                        ),
                                    ),
                                    'common_typeahead_remote' => array(
                                        'assets'  => array(
                                            'common/js/typeaheadRemote.js',
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
                                            'media' => 'print'
                                        ),
                                    ),
                                    'admin_js' => array(
                                        'assets'  => array(
                                            'admin/js/*.js',
                                        ),
                                    ),
                                    
                                    'site_css' => array(
                                    	'assets' => array(
                                    		'site/less/base.less',
                                    	),
                                    	'filters' => array(
                                    		'site_less' => array(
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
                                            'output' => 'site_css.css'
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
                                    'bootstrap_responsive_css' => array(
                                    	'assets' => array(
                                    		'bootstrap/less/responsive.less',
                                    	),
                                    	'filters' => array(
                                    		'bootstrap_responsive_less' => array(
                                    			'name' => 'LessFilter',
                                    			'parameters' => array(
                                    				'nodeBin'   => '/usr/local/bin/node',
                                    				'nodePaths' => array(
                                    					'/usr/local/lib/node_modules',
                                    				),
                                    				'compress'  => false,
                                    			),
                                    		),
                                    	),
                                    	'options' => array(
                                            'output' => 'bootstrap_responsive_css.css',
                                        ),
                                    ),
                                    'bootstrap_js_alert' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-alert.js',
                                        ),
                                    ),
                                    'bootstrap_js_button' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-button.js',
                                        ),
                                    ),
                                    'bootstrap_js_carousel' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-carousel.js',
                                        ),
                                    ),
                                    'bootstrap_js_collapse' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-collapse.js',
                                        ),
                                    ),
                                    'bootstrap_js_dropdown' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-dropdown.js',
                                        ),
                                    ),
                                    'bootstrap_js_modal' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-modal.js',
                                        ),
                                    ),
                                    'bootstrap_js_popover' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-popover.js',
                                        ),
                                    ),
                                    'bootstrap_js_scrollspy' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-scrollspy.js',
                                        ),
                                    ),
                                    'bootstrap_js_tab' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-tab.js',
                                        ),
                                    ),
                                    'bootstrap_js_tooltip' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-tooltip.js',
                                        ),
                                    ),
                                    'bootstrap_js_transition' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-transition.js',
                                        ),
                                    ),
                                    'bootstrap_js_typeahead' => array(
                                        'assets' => array(
                                            'bootstrap/js/bootstrap-typeahead.js',
                                        ),
                                    ),
                                    
                                    'gollum_css' => array(
                                        'assets' => array(
                                            'gollum/css/editor.css'
                                        ),
                                    ),
                                    'gollum_js' => array(
                                        'assets' => array(
                                            'gollum/js/editor.js',
                                            'gollum/js/markdown.js',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
                       
            'authentication' => array(
            	'parameters' => array(
            		'adapter' => 'authentication_credentialadapter',
            		'service' => 'authentication_doctrineservice',
            	),
            ),
            'authentication_action' => array(
                'parameters' => array(
                    'entityManager' => 'doctrine_em',
                    'mailTransport' => 'mail_transport',
                )
            ),
            'authentication_credentialadapter' => array(
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
            		'action'        => 'authentication_action',
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
            
            'translator' => array(
            	'parameters' => array(
            	    'adapter' => 'ArrayAdapter',
            		'translations' => array(
                		'common_site_en' => array(
                			'content' => __DIR__ . '/../translations/site.en.php',
                			'locale' => 'en',
                		),
                		'common_site_nl' => array(
                			'content' => __DIR__ . '/../translations/site.nl.php',
                			'locale' => 'nl',
                		),
                        'validators_en' => array(
                        	'content' => 'vendor/ZendFramework/resources/languages/en/Zend_Validate.php',
                        	'locale' => 'en',
                        ),
                        'validators_nl' => array(
                        	'content' => 'vendor/ZendFramework/resources/languages/nl/Zend_Validate.php',
                        	'locale' => 'nl',
                        ),
                        'countries_en' => array(
                        	'content' => __DIR__ . '/../translations/countries.en.php',
                        	'locale' => 'en',
                        ),
                        'countries_nl' => array(
                        	'content' => __DIR__ . '/../translations/countries.nl.php',
                        	'locale' => 'nl',
                        ),
                    ),
            	),
            ),
            
            'Zend\View\Helper\Doctype' => array(
                'parameters' => array(
                    'doctype' => 'HTML5',
                ),
            ),
            'Zend\Mvc\View\RouteNotFoundStrategy' => array(
                'parameters' => array(
                    'displayNotFoundReason' => true,
                    'displayExceptions'     => true,
                    'notFoundTemplate'      => 'error/404',
                ),
            ),
            'Zend\Mvc\View\ExceptionStrategy' => array(
                'parameters' => array(
                    'displayExceptions' => true,
                    'exceptionTemplate' => 'error/index',
                ),
            ),
            
            'Zend\Mvc\Controller\ActionController' => array(
                'parameters' => array(
                    'broker'       => 'Zend\Mvc\Controller\PluginBroker',
                ),
            ),
            'Zend\Mvc\Controller\PluginBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\Mvc\Controller\PluginLoader',
                ),
            ),
            
            'Zend\View\Resolver\AggregateResolver' => array(
                'injections' => array(
                    'Zend\View\Resolver\TemplateMapResolver',
                    'Zend\View\Resolver\TemplatePathStack',
                ),
            ),
            'Zend\View\Resolver\TemplateMapResolver' => array(
                'parameters' => array(
                    'map'  => array(
                        'layout' => __DIR__ . '/../layouts/layout.twig',
                    ),
                ),
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'common_layouts' => __DIR__ . '/../layouts',
                        'common_views' => __DIR__ . '/../views',
                    ),
                    'defaultSuffix' => 'twig'
                ),
            ),
            'ZfTwig\TwigRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
            ),
            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
            ),
            
            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'index' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/',
                                'constraints' => array(
                                ),
                                'defaults' => array(
                                    'controller' => 'index',
                                    'action'     => 'index',
                                ),
                            ),
                        ),
                        'account' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/account[/:action[/code/:code]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'      => '[a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'account',
                                    'action'     => 'index',
                                ),
                            ),
                        ),
                        'common_install' => array(
                        	'type'    => 'Zend\Mvc\Router\Http\Segment',
                        	'options' => array(
                        		'route' => '/admin/install/common',
                        		'defaults' => array(
                        			'controller' => 'common_install',
                        			'action'     => 'index',
                        		),
                        	),
                        ),
                    	'admin_academic' => array(
                    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
                    	    'options' => array(
                    	        'route'    => '/admin/academic[/:action[/:id][/page/:page][/:field/:string]]',
                    	        'constraints' => array(
                    	        	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    	        	'id'      => '[0-9]*',
                    				'page'    => '[0-9]*',
                    				'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    				'string'  => '[a-zA-Z][%a-zA-Z0-9_-]*',
                    	        ),
                    	        'defaults' => array(
                    	            'controller' => 'admin_academic',
                    	            'action'     => 'manage',
                    	        ),
                    	    ),
                    	),
                    	'admin_academic_typeahead' => array(
                    		'type'    => 'Zend\Mvc\Router\Http\Segment',
                    		'options' => array(
                    			'route' => '/admin/academic/typeahead[/:string]',
                    			'constraints' => array(
                    				'string'       => '[%a-zA-Z0-9_-]*',
                    			),
                    			'defaults' => array(
                    				'controller' => 'admin_academic',
                    				'action'     => 'typeahead',
                    			),
                    		),
                    	),
                    	'admin_config' => array(
                    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
                    	    'options' => array(
                    	        'route'    => '/admin/config[/:action[/key:key]]',
                    	        'constraints' => array(
                    	        	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    	        	'key'     => '[a-zA-Z][\.a-zA-Z0-9_-]*',
                    	        ),
                    	        'defaults' => array(
                    	            'controller' => 'admin_config',
                    	            'action'     => 'manage',
                    	        ),
                    	    ),
                    	),
                    	'admin_index' => array(
                    	    'type'    => 'Zend\Mvc\Router\Http\Segment',
                    	    'options' => array(
                    	        'route'    => '/admin[/:action]',
                    	        'defaults' => array(
                    	            'controller' => 'admin_index',
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
                    	        'route'    => '/admin/role[/:action[/name:name][/page/:page]]',
                    	        'constraints' => array(
                    	        	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    	        	'name'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    				'page'    => '[0-9]*',
                    	        ),
                    	        'defaults' => array(
                    	            'controller' => 'admin_role',
                    	            'action'     => 'manage',
                    	        ),
                    	    ),
                    	),
                    ),
                ),
            ),
        ),
    ),
);
