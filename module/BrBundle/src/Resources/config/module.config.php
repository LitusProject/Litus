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
            'br_install' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/br',
                    'defaults' => array(
                        'controller' => 'br_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_company' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/company[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_company',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_company_event' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/company/event[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_company_event',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_company_internship' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/company/internship[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_company_internship',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_company_vacancy' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/company/vacancy[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_company_vacancy',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_company_user' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/user[/:action[/:id]][/page/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_company_user',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_section' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/section[/:action[/:id[/:confirm]]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'confirm' => '[01]',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_section',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'corporate_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/corporate[/:action]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'corporate_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'corporate_auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/corporate/auth[/:action]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'corporate_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'br_layout' => __DIR__ . '/../layouts',
            'br_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'BrBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'brbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'br_install'               => 'BrBundle\Controller\Admin\InstallController',

            'admin_company'            => 'BrBundle\Controller\Admin\CompanyController',
            'admin_company_event'      => 'BrBundle\Controller\Admin\Company\EventController',
            'admin_company_internship' => 'BrBundle\Controller\Admin\Company\InternshipController',
            'admin_company_vacancy'    => 'BrBundle\Controller\Admin\Company\VacancyController',
            'admin_company_user'       => 'BrBundle\Controller\Admin\Company\UserController',
            'admin_section'            => 'BrBundle\Controller\Admin\SectionController',

            'corporate_index'          => 'BrBundle\Controller\Corporate\IndexController',
            'corporate_auth'           => 'BrBundle\Controller\Corporate\AuthController',
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'brbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'corporate_css' => array(
                        'assets' => array(
                            'corporate/less/base.less',
                        ),
                        'filters' => array(
                            'corporate_less' => array(
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
                            'output' => 'corporate_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
