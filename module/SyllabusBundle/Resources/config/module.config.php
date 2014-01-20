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
            'syllabus_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/syllabus[/]',
                    'defaults' => array(
                        'controller' => 'syllabus_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'syllabus_admin_update' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/update[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_update',
                        'action'     => 'index',
                    ),
                ),
            ),
            'syllabus_admin_academic' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/academic[/:action[/:id][/page/:page][/:academicyear][/:field/:string]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'page'         => '[0-9]+',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_academic',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'syllabus_admin_group' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/group[/:action[/:id][/page/:page][/:academicyear]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'page'         => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_group',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'syllabus_admin_study' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/study[/:action[/:id][/:academicyear][/:field/:string][/page/:page]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                        'page'         => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_study',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'syllabus_admin_study_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/study/typeahead/:academicyear[/:string][/]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_study',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'syllabus_admin_subject' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/subject[/:action[/:id][/:academicyear][/:field/:string][/page/:page]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                        'page'         => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_subject',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'syllabus_admin_subject_comment' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/subject/comments[/:action[/:id][/:academicyear][/page/:page]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'page'         => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_subject_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'syllabus_admin_subject_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/subject/typeahead/:academicyear[/:string][/]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_subject',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'syllabus_admin_prof' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/prof[/:action[/:id]][/:academicyear][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_prof',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'syllabus_admin_prof_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/syllabus/prof/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'  => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_admin_prof',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'subject_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/syllabus/subject/typeahead/:academicyear[/:string][/]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'syllabus_subject',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'syllabus_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'SyllabusBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'syllabusbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'syllabus_install'               => 'SyllabusBundle\Controller\Admin\InstallController',

            'syllabus_admin_update'          => 'SyllabusBundle\Controller\Admin\UpdateController',
            'syllabus_admin_academic'        => 'SyllabusBundle\Controller\Admin\AcademicController',
            'syllabus_admin_group'           => 'SyllabusBundle\Controller\Admin\GroupController',
            'syllabus_admin_study'           => 'SyllabusBundle\Controller\Admin\StudyController',
            'syllabus_admin_subject'         => 'SyllabusBundle\Controller\Admin\SubjectController',
            'syllabus_admin_subject_comment' => 'SyllabusBundle\Controller\Admin\Subject\CommentController',
            'syllabus_admin_prof'            => 'SyllabusBundle\Controller\Admin\ProfController',
            'syllabus_subject'               => 'SyllabusBundle\Controller\SubjectController',
        ),
    ),
);
