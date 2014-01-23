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
            'secretary_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/secretary[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'secretary_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'secretary_admin_registration' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/secretary/registration[/:action[/:id][/organization/:organization]][/:academicyear][/:field/:string][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'organization' => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'secretary_admin_registration',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'secretary_admin_promotion' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/secretary/promotion[/:action[/:id][/page/:page]][/:academicyear][/:field/:string][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'page'         => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'secretary_admin_promotion',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'secretary_registration' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/secretary/registration[/:action][/identification/:identification[/hash/:hash]][/]',
                    'constraints' => array(
                        'language'       => '[a-z]{2}',
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'secretary_registration',
                        'action'     => 'add',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'secretary_view' => __DIR__ . '/../views',
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'SecretaryBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'secretarybundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'secretary_install'              => 'SecretaryBundle\Controller\Admin\InstallController',
            'secretary_admin_registration'   => 'SecretaryBundle\Controller\Admin\RegistrationController',
            'secretary_admin_promotion'      => 'SecretaryBundle\Controller\Admin\PromotionController',

            'secretary_registration'         => 'SecretaryBundle\Controller\RegistrationController',
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'secretarybundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'secretary_css' => array(
                        'assets' => array(
                            'secretary/less/study.less',
                        ),
                        'filters' => array(
                            'secretary_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
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
                            'output' => 'secretary.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
