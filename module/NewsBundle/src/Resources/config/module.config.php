<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'news_install' => 'NewsBundle\Controller\Admin\InstallController',
                'admin_news'   => 'NewsBundle\Controller\Admin\NewsController',

                'common_news'  => 'NewsBundle\Controller\NewsController',
            ),

            'doctrine_config' => array(
                'parameters' => array(
                    'entityPaths' => array(
                        'newsbundle' => __DIR__ . '/../../Entity',
                    ),
                ),
            ),

            'translator' => array(
                'parameters' => array(
                    'adapter' => 'ArrayAdapter',
                    'translations' => array(
                        'news_site_en' => array(
                            'content' => __DIR__ . '/../translations/site.en.php',
                            'locale' => 'en',
                        ),
                        'news_site_nl' => array(
                            'content' => __DIR__ . '/../translations/site.nl.php',
                            'locale' => 'nl',
                        ),
                    ),
                ),
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'news_views' => __DIR__ . '/../views',
                    ),
                ),
            ),

            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'news_install' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/admin/install/news',
                                'constraints' => array(
                                ),
                                'defaults' => array(
                                    'controller' => 'news_install',
                                    'action'     => 'index',
                                ),
                            ),
                        ),
                        'admin_news' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/admin/site/news[/:action[/:id]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'      => '[0-9]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'admin_news',
                                    'action'     => 'manage',
                                ),
                            ),
                        ),
                        'common_news' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/news[/:action[/:name]][/page/:page]',
                                'constraints' => array(
                                    'action'   => '[a-zA-Z0-9_-]*',
                                    'name'     => '[a-zA-Z0-9_-]*',
                                    'page'     => '[0-9]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'common_news',
                                    'action'     => 'overview',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
