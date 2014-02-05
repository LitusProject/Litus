<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
            'gallery_admin_gallery' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/site/gallery[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'gallery_admin_gallery',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'gallery' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/gallery[/:action[/:name]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'name'     => '[a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'gallery',
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
            'gallery_install'       => 'GalleryBundle\Controller\Admin\InstallController',
            'gallery_admin_gallery' => 'GalleryBundle\Controller\Admin\GalleryController',

            'gallery'               => 'GalleryBundle\Controller\GalleryController',
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
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.nl.php',
                'locale'   => 'nl'
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
                                'name' => '\CommonBundle\Component\Assetic\Filter\Less',
                            ),
                        ),
                        'options' => array(
                            'output' => 'gallery_css.css',
                        ),
                    ),
                    'gallery_js' => array(
                        'assets'  => array(
                            'common/js/imageGallery.js',
                        ),
                        'filters' => array(
                            'gallery_js_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                    ),
                    'plupload_js' => array(
                        'assets'  => array(
                            'plupload/js/plupload.full.js',
                            'plupload/js/bootstrap/uploadkit.js',
                        ),
                        'filters' => array(
                            'plupload_js_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                    ),
                    'plupload_css' => array(
                        'assets'  => array(
                            'plupload/js/bootstrap/uploadkit.css',
                        ),
                        'filters' => array(
                            'plupload_css_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Css',
                            ),
                        ),
                        'options' => array(
                            'output' => 'plupload_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
