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

    'controllers' => array(
        'gallery_install'       => 'GalleryBundle\Controller\Admin\InstallController',
        'gallery_admin_gallery' => 'GalleryBundle\Controller\Admin\GalleryController',

        'gallery'               => 'GalleryBundle\Controller\GalleryController',
    ),
);
