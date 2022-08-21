<?php

return array(
    'routes' => array(
        'publication_admin_publication' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/publication[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_admin_publication',
                    'action'     => 'manage',
                ),
            ),
        ),
        'publication_admin_edition_pdf' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/edition/pdf[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_admin_edition_pdf',
                    'action'     => 'manage',
                ),
            ),
        ),
        'publication_admin_edition_html' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/edition/html[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_admin_edition_html',
                    'action'     => 'manage',
                ),
            ),
        ),
        'publication_admin_video' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/video[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_admin_video',
                    'action'     => 'manage',
                ),
            ),
        ),
        'publication_archive' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/archive[/:action[/:publication[/:year]]][/]',
                'constraints' => array(
                    'action'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'publication' => '[0-9]*',
                    'year'        => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_archive',
                    'action'     => 'overview',
                ),
            ),
        ),
        'publication_edition_html' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/archive/html[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_edition_html',
                    'action'     => 'view',
                ),
            ),
        ),
        'publication_video' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/archive/videos[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'publication_video',
                    'action'     => 'view',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'publication_admin_publication'  => 'PublicationBundle\Controller\Admin\PublicationController',
        'publication_admin_edition_pdf'  => 'PublicationBundle\Controller\Admin\Edition\PdfController',
        'publication_admin_edition_html' => 'PublicationBundle\Controller\Admin\Edition\HtmlController',
        'publication_admin_video'        => 'PublicationBundle\Controller\Admin\VideoController',

        'publication_archive'            => 'PublicationBundle\Controller\Archive\ArchiveController',
        'publication_edition_html'       => 'PublicationBundle\Controller\Edition\HtmlController',
        'publication_video'              => 'PublicationBundle\Controller\Video\VideoController',
    ),
);
