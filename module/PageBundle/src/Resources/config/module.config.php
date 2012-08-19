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
                'page_install'        => 'PageBundle\Controller\Admin\InstallController',
                'admin_page'          => 'PageBundle\Controller\Admin\PageController',
                'admin_page_category' => 'PageBundle\Controller\Admin\CategoryController',

                'page'                => 'PageBundle\Controller\PageController',
            ),

            'doctrine_config' => array(
                'parameters' => array(
                    'entityPaths' => array(
                        'pagebundle' => __DIR__ . '/../../Entity',
                    ),
                ),
            ),

            'translator' => array(
                'parameters' => array(
                    'adapter' => 'ArrayAdapter',
                    'translations' => array(
                        'page_site_en' => array(
                            'content' => __DIR__ . '/../translations/site.en.php',
                            'locale'  => 'en',
                        ),
                        'page_site_nl' => array(
                            'content' => __DIR__ . '/../translations/site.nl.php',
                            'locale'  => 'nl',
                        ),
                    ),
                ),
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'page_views' => __DIR__ . '/../views',
                    ),
                ),
            ),

            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'page_install' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => '/admin/install/page',
                                'constraints' => array(
                                ),
                                'defaults' => array(
                                    'controller' => 'page_install',
                                    'action'     => 'index',
                                ),
                            ),
                        ),
                        'admin_page' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => '/admin/site/page[/:action[/:id]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'      => '[0-9]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'admin_page',
                                    'action'     => 'manage',
                                ),
                            ),
                        ),
                        'admin_page_category' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => '/admin/site/page/category[/:action[/:id]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'      => '[0-9]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'admin_page_category',
                                    'action'     => 'manage',
                                ),
                            ),
                        ),
                        'page' => array(
                            'type' => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '[/:language]/page[/:name]',
                                'constraints' => array(
                                    'name'     => '[a-zA-Z0-9_-]*',
                                    'language' => '[a-zA-Z][a-zA-Z_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'page',
                                    'action'     => 'view',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
