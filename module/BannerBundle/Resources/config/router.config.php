<?php

return array(
    'routes' => array(
        'banner_admin_banner' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/banner[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'banner_admin_banner',
                    'action'     => 'manage',
                ),
            ),
        ),
        'banner' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/banner[/:action[/image/:image]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'image'  => '[a-zA-Z0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'banner',
                    'action'     => 'view',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'banner_admin_banner' => 'BannerBundle\Controller\Admin\BannerController',

        'banner' => 'BannerBundle\Controller\BannerController',
    ),
);
