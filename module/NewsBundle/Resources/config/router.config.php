<?php

return array(
    'routes' => array(
        'news_admin_news' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/news[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'news_admin_news',
                    'action'     => 'manage',
                ),
            ),
        ),
        'news' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/news[/:action[/:name][/page/:page]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z0-9_-]*',
                    'name'     => '[a-zA-Z0-9_-]*',
                    'page'     => '[0-9]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'news',
                    'action'     => 'overview',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'news_admin_news' => 'NewsBundle\Controller\Admin\NewsController',

        'news'            => 'NewsBundle\Controller\NewsController',
    ),
);
