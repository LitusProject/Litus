<?php

return array(
    'routes' => array(
        'page_admin_page' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/page[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'page_admin_page',
                    'action'     => 'manage',
                ),
            ),
        ),
        'page_admin_category' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/page/category[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'page_admin_category',
                    'action'     => 'manage',
                ),
            ),
        ),
        'page_admin_link' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/page/link[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'page_admin_link',
                    'action'     => 'manage',
                ),
            ),
        ),
        'page_admin_page_typeahead' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/page/typeahead[/:string][/]',
                'constraints' => array(
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'page_admin_page',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'page_admin_categorypage' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/page/categorypage[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'page_admin_categorypage',
                    'action'     => 'manage',
                ),
               ),
        ),
        'page_admin_categorypage_frame' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/page/categorypage/:category_page_id/frame[/:action[/:frame_id][/:name][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'category_page_id'     => '[0-9]*',
                    'frame_id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'page_admin_categorypage_frame',
                    'action'     => 'manage',
                ),
            ),
        ),
        'page_link' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/link[/:id][/]',
                'constraints' => array(
                    'id'       => '[0-9]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'page_link',
                    'action'     => 'view',
                ),
            ),
        ),
        'page_categorypage' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/category[/:name][/:action[/:poster_name]][/]',
                'constraints' => array(
                    'name'   => '[a-zA-Z0-9_-]*',
                    'poster_name'   => '[a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'page_categorypage',
                    'action'     => 'view',
                ),
            ),
        ),
        'page' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/page[/parent/:parent][/name/:name][/]',
                'constraints' => array(
                    'parent'   => '[a-zA-Z0-9_-]*',
                    'name'     => '[a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'page',
                    'action'     => 'view',
                ),
            ),
        ),
        'page_file' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/page/file/:name[/]',
                'constraints' => array(
                    'name' => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'page',
                    'action'     => 'file',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'page_admin_page'     => 'PageBundle\Controller\Admin\PageController',
        'page_admin_category' => 'PageBundle\Controller\Admin\CategoryController',
        'page_admin_link'     => 'PageBundle\Controller\Admin\LinkController',
        'page_admin_categorypage' => 'PageBundle\Controller\Admin\CategoryPageController',
        'page_admin_categorypage_frame' => 'PageBundle\Controller\Admin\FrameController',

        'page_link'           => 'PageBundle\Controller\LinkController',
        'page'                => 'PageBundle\Controller\PageController',
        'page_categorypage'   => 'PageBundle\Controller\CategoryPageController',
    ),
);
