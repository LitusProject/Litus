<?php

return array(
    'router' => array(
        'routes' => array(
            'gallery_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/gallery[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'gallery_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_gallery' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/site/gallery[/:action[/:id]][/page/:page][/]',
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
                    'route' => '[/:language]/gallery[/:action[/:id]][/]',
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
    'view_manager' => array(
        'template_path_stack' => array(
            'gallery_view' => __DIR__ . '/../views',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'gallery_install'  => 'GalleryBundle\Controller\Admin\InstallController',
            'admin_gallery'    => 'GalleryBundle\Controller\Admin\GalleryController',

            'common_gallery'   => 'GalleryBundle\Controller\GalleryController',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'GalleryBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
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
                        'assets' => array(
                            'common/less/gallery.less',
                            'common/less/imageGallery.min.css',
                        ),
                        'filters' => array(
                            'gallery_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'gallery_css.css',
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
