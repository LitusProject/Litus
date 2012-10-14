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
                    'route' => '/admin/install/br[/]',
                    'defaults' => array(
                        'controller' => 'br_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_company' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company[/:action[/:id]][/]',
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
                    'route' => '/admin/company/event[/:action[/:id]][/]',
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
            'admin_company_job' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/job[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_company_job',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_company_user' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/user[/:action[/:id]][/page/:page][/]',
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
                    'route' => '/admin/section[/:action[/:id[/:confirm]]][/]',
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
                    'route' => '[/:language]/corporate[/:action][/]',
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
                    'route' => '[/:language]/corporate/auth[/:action][/]',
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
            'career_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'career_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'career_company' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/company[/:action[/:company]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'company'     => '[a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'career_company',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'career_event' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/event[/:action][/page/:page][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                        'page'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'career_event',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'career_vacancy' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/vacancy[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9_-]*',
                        'language' => '[a-z]{2}',
                        'page'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'career_vacancy',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'career_internship' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/internship[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9_-]*',
                        'language' => '[a-z]{2}',
                        'page'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'career_internship',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'career_file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/career/company/file/:name[/]',
                    'constraints' => array(
                        'name'     => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'career_company',
                        'action'     => 'file',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/career.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/career.nl.php',
                'locale'   => 'nl'
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
            'admin_company_job' => 'BrBundle\Controller\Admin\Company\JobController',
            'admin_company_user'       => 'BrBundle\Controller\Admin\Company\UserController',
            'admin_section'            => 'BrBundle\Controller\Admin\SectionController',

            'corporate_index'          => 'BrBundle\Controller\Corporate\IndexController',
            'corporate_auth'           => 'BrBundle\Controller\Corporate\AuthController',

            'career_index'             => 'BrBundle\Controller\Career\IndexController',
            'career_vacancy'           => 'BrBundle\Controller\Career\VacancyController',
            'career_internship'        => 'BrBundle\Controller\Career\InternshipController',
            'career_event'             => 'BrBundle\Controller\Career\EventController',
            'career_company'           => 'BrBundle\Controller\Career\CompanyController',
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
                    'career_rowlink' => array(
                        'assets'  => array(
                            'career/js/bootstrap-rowlink.js',
                        ),
                    ),
                    'career_css' => array(
                        'assets' => array(
                            'career/less/career.less',
                        ),
                        'filters' => array(
                            'career_less' => array(
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
                            'output' => 'career_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
