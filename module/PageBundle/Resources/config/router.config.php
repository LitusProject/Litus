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
        'page_admin_page' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/site/page[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'page_admin_page',
                    'action'     => 'manage',
                ),
            ),
        ),
        'page_admin_category' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/site/page/category[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'page_admin_category',
                    'action'     => 'manage',
                ),
            ),
        ),
        'page_admin_link' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/site/page/link[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'page_admin_link',
                    'action'     => 'manage',
                ),
            ),
        ),

        'page_link' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/link[/:id][/]',
                'constraints' => array(
                    'id'       => '[0-9]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'page_link',
                    'action'     => 'view',
                ),
            ),
        ),
        'page' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/page[/parent/:parent][/name/:name][/]',
                'constraints' => array(
                    'parent'   => '[a-zA-Z0-9_-]*',
                    'name'     => '[a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'page',
                    'action'     => 'view',
                ),
            ),
        ),
        'page_file' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/page/file/:name[/]',
                'constraints' => array(
                    'name'     => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
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

        'page_link'           => 'PageBundle\Controller\LinkController',
        'page'                => 'PageBundle\Controller\PageController',
    ),
);
