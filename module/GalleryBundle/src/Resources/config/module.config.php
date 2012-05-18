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
                'install_gallery'  => 'GalleryBundle\Controller\Admin\InstallController',
                'admin_gallery'    => 'GalleryBundle\Controller\Admin\GalleryController',

                'common_gallery'   => 'GalleryBundle\Controller\GalleryController',
            ),
            
            'assetic_configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'modules'      => array(
                            'gallerybundle' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'gallery_css' => array(
                                        'assets'  => array(
                                            'common/css/gallery.css',
                                            'common/css/imageGallery.min.css',
                                        ),
                                    ),
                                    'gallery_js' => array(
                                        'assets'  => array(
                                            'common/js/imageGallery.pack.js',
                                        ),
                                    ),
                                	'plupload_js' => array(
                                	    'assets'  => array(
                                	        'plupload/js/plupload.full.js',
                                	        'plupload/js/bootstrap/uploadkit.js',
                                	    ),
                                	),
                                	'plupload_css' => array(
                                	    'assets'  => array(
                                	        'plupload/js/bootstrap/uploadkit.css',
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
                		'gallerybundle' => __DIR__ . '/../../Entity',
                	),
                ),
            ),
            
            'translator' => array(
            	'parameters' => array(
        		    'adapter' => 'ArrayAdapter',
        			'translations' => array(
        				'faq_admin_en' => array(
                			'content' => __DIR__ . '/../translations/admin.en.php',
                			'locale' => 'en',
                		),
                		'faq_admin_nl' => array(
                			'content' => __DIR__ . '/../translations/admin.nl.php',
                			'locale' => 'nl',
                		),
            		),
            	),
            ),
        ),
    ),
    'routes' => array(
        'install_gallery' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/install/gallery',
                'constraints' => array(
                ),
                'defaults' => array(
                    'controller' => 'install_gallery',
                    'action'     => 'index',
                ),
            ),
        ),
        'admin_gallery' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/content/gallery[/:action[/:id]]',
                'constraints' => array(
                	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                	'id'      => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'admin_gallery',
                    'action'     => 'manage',
                ),
            ),
        ),
        /*'common_gallery' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '[/:language]/gallery[/:action[/:id]]',
                'constraints' => array(
                	'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                	'id'  => '[a-zA-Z0-9_-]*',
                    'language' => '[a-zA-Z][a-zA-Z_-]*',
                ),
                'defaults' => array(
                    'controller' => 'common_gallery',
                    'action'     => 'overview',
                ),
            ),
        ),*/
	),
);
