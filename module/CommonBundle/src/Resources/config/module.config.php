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
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'commonbundle_authentication'                 => 'CommonBundle\Component\Authentication\Authentication',
                'commonbundle_authentication_doctrineadapter' => 'CommonBundle\Component\Authentication\Adapter\Doctrine',
                'commonbundle_authentication_doctrineservice' => 'CommonBundle\Component\Authentication\Service\Doctrine',
                'commonbundle_error'                          => 'CommonBundle\Controller\ErrorController',              
            ),
            'commonbundle_authentication' => array(
            	'parameters' => array(
            		'adapter' => 'commonbundle_authentication_doctrineadapter',
            		'service' => 'commonbundle_authentication_doctrineservice',
            	),
            ),
            'commonbundle_authentication_doctrineadapter' => array(
            	'parameters' => array(
            		'entityManager'  => 'doctrine_em',
            		'entityName'     => '"CommonBundle\Entity\Users\Person"',
            		'identityColumn' => 'username',
            	),
            ),
            'commonbundle_authentication_doctrineservice' => array(
            	'parameters' => array(
            		'entityManager' => 'doctrine_em',
            		'entityName'    => '"CommonBundle\Entity\Users\Session"',
            		'expire'        => '2678400',
            	),
            ),
            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'cacheEnabled' => true,
                        'cachePath'    => __DIR__ . '/../../../../../data/cache',
                        'controllers'  => $asseticConfig['controllers'],
                        'routes' => $asseticConfig['routes'],
                        'modules' => array(
                            'commonbundle' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'admin_base_css' => array(
                                        'assets'  => array(
                                            'admin_base/stylesheet/css/*.css',
                                        ),
                                        'filters' => array(),
                                        'options' => array(),
                                    ),
                                    'admin_base_js' => array(
                                        'assets'  => array(
                                            'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
                                            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js',
                                            'admin_base/stylesheet/css/*.css',
                                            'admin_base/js/*.js',
                                        ),
                                        'filters' => array(),
                                        'options' => array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'routes' => array(
	    'commonbundle_default' => array(
	        'type'    => 'Zend\Mvc\Router\Http\Segment',
	        'options' => array(
	            'route'    => '/[:controller[/:action]]',
	            'constraints' => array(
	                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
	                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
	            ),
	            'defaults' => array(
	                'controller' => 'commonbundle_error',
	                'action'     => 'error',
	            ),
	        ),
	    ),
	),
);