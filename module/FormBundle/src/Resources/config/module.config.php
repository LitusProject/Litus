<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'router' => array(
        'routes' => array(
            'form_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/form[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'form_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_form' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/form[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_form',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_form_field' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/form/field[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_form_field',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_form_viewer' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/form/viewer[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_form_viewer',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'form_view' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/form[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'form_view',
                        'action'     => 'view',
                    ),
                ),
            ),
            'form_manage' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/form/manage[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'form_manage',
                        'action'     => 'index',
                    ),
                ),
            ),
            'form_manage_auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/form/manage/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'form_manage_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'form_layout' => __DIR__ . '/../layouts',
            'form_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'FormBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'formbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'form_install'      => 'FormBundle\Controller\Admin\InstallController',
            'admin_form'        => 'FormBundle\Controller\Admin\FormController',
            'admin_form_field'  => 'FormBundle\Controller\Admin\FieldController',
            'admin_form_viewer' => 'FormBundle\Controller\Admin\ViewerController',
            'form_view'         => 'FormBundle\Controller\FormController',
            'form_manage'       => 'FormBundle\Controller\Manage\FormController',
            'form_manage_auth'  => 'FormBundle\Controller\Manage\AuthController',
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/manage.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/manage.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'formbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'form_manage_css' => array(
                        'assets' => array(
                            'manage/less/base.less',
                        ),
                        'filters' => array(
                            'form_manage_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'form_manage_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
