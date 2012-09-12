<?php

return array(
    'router' => array(
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
                    'route'    => '/admin/content/gallery[/:action[/:id]][/page/:page]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_gallery',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_gallery' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '[/:language]/gallery[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'common_gallery',
                        'action'     => 'overview',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/admin.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/admin.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'gallery_view' => __DIR__ . '/../views',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'install_gallery'  => 'GalleryBundle\Controller\Admin\InstallController',
            'admin_gallery'    => 'GalleryBundle\Controller\Admin\GalleryController',

            'common_gallery'   => 'GalleryBundle\Controller\GalleryController',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'GalleryBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'gallerybundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
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
);