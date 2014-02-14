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
            'news_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/news[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'news_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'news_admin_news' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/site/news[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'news_admin_news',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'news' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/news[/:action[/:name][/page/:page]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z0-9_-]*',
                        'name'     => '[a-zA-Z0-9_-]*',
                        'page'     => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'news',
                        'action'     => 'overview',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../translations',
                'pattern'  => 'site.%s.php',
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'news_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'NewsBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'newsbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'news_install'    => 'NewsBundle\Controller\Admin\InstallController',
            'news_admin_news' => 'NewsBundle\Controller\Admin\NewsController',

            'news'            => 'NewsBundle\Controller\NewsController',
        ),
    ),
);
