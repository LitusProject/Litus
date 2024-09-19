<?php

return array(
    'routes' => array(
        'gallery_admin_gallery' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/gallery[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'gallery_admin_gallery',
                    'action'     => 'manage',
                ),
            ),
        ),
        /*
        'gallery' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/gallery[/:action[/:name]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'name'     => '[a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'gallery',
                    'action'     => 'overview',
                ),
            ),
        ),
        */
    ),

    'controllers' => array(
        'gallery_admin_gallery' => 'GalleryBundle\Controller\Admin\GalleryController',

        //'gallery'               => 'GalleryBundle\Controller\GalleryController',
    ),
);
