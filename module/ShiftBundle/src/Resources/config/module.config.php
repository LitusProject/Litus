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
                'api_install' => 'ApiBundle\Controller\Admin\InstallController',
                'admin_key'   => 'ApiBundle\Controller\Admin\KeyController',

                'api_auth'    => 'ApiBundle\Controller\AuthController',
            ),

            'doctrine_config' => array(
                'parameters' => array(
                    'entityPaths' => array(
                        'apibundle' => __DIR__ . '/../../Entity',
                    ),
                ),
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'api_views' => __DIR__ . '/../views',
                    ),
                ),
            ),

            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'api_install' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/admin/install/api',
                                'constraints' => array(
                                ),
                                'defaults' => array(
                                    'controller' => 'api_install',
                                    'action'     => 'index',
                                ),
                            ),
                        ),
                        'admin_key' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/admin/api/key[/:action[/:id]]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'      => '[0-9]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'admin_key',
                                    'action'     => 'manage',
                                ),
                            ),
                        ),
                        'api_auth' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/api/auth[/:action]',
                                'constraints' => array(
                                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'api_auth',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
