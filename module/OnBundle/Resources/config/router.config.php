<?php

return array(
    'routes' => array(
        'on_admin_slug' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/on/slug[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-z0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'on_admin_slug',
                    'action'     => 'manage',
                ),
            ),
        ),
        'on_redirect' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/on[/:name][/]',
                'constraints' => array(
                    'name' => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'on_redirect',
                    'action'     => 'index',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'on_admin_slug' => 'OnBundle\Controller\Admin\SlugController',

        'on_redirect'   => 'OnBundle\Controller\RedirectController',
    ),
);
