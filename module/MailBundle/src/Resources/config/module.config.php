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
            'mail_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/mail[/]',
                    'defaults' => array(
                        'controller' => 'mail_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_mail' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/mail/groups[/:action[/:type/:group]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'group'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail',
                        'action'     => 'groups',
                    ),
                ),
            ),
            'admin_mail_bakske' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/mail/bakske[/:action][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail_bakske',
                        'action'     => 'send',
                    ),
                ),
            ),
            'admin_mail_prof' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/mail/prof[/:action][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail_prof',
                        'action'     => 'cudi',
                    ),
                ),
            ),
            'admin_mail_study' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/mail/study[/:action][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail_study',
                        'action'     => 'send',
                    ),
                ),
            ),
            'admin_mail_bakske' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/mail/bakske[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail_bakske',
                        'action'     => 'send',
                    ),
                ),
            ),
            'admin_mail_list' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/mail/list[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail_list',
                        'action'     => 'manage',
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'MailBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'mailbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'mail_view' => __DIR__ . '/../views',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'mail_install'      => 'MailBundle\Controller\Admin\InstallController',
            'admin_mail'        => 'MailBundle\Controller\Admin\MailController',
            'admin_mail_bakske' => 'MailBundle\Controller\Admin\BakskeController',
            'admin_mail_prof'   => 'MailBundle\Controller\Admin\ProfController',
            'admin_mail_study'  => 'MailBundle\Controller\Admin\StudyController',
            'admin_mail_bakske' => 'MailBundle\Controller\Admin\BakskeController',
            'admin_mail_list'   => 'MailBundle\Controller\Admin\ListController',
        ),
    ),
);
