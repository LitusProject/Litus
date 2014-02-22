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
    'routes' => array(
        'mail_admin_alias' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/alias[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_alias',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_bakske' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/bakske[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_bakske',
                    'action'     => 'send',
                ),
            ),
        ),
        'mail_admin_group' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/groups[/:action[/:type/:group]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'group'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_group',
                    'action'     => 'groups',
                ),
            ),
        ),
        'mail_admin_list' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/list[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_list',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_message' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/message[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[a-z0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_message',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_prof' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/prof[/:action][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_prof',
                    'action'     => 'cudi',
                ),
            ),
        ),
        'mail_admin_study' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/study[/:action][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_study',
                    'action'     => 'send',
                ),
            ),
        ),
        'mail_admin_volunteer' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/mail/volunteer[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'mail_admin_volunteer',
                    'action'     => 'send',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'mail_admin_alias'     => 'MailBundle\Controller\Admin\AliasController',
        'mail_admin_bakske'    => 'MailBundle\Controller\Admin\BakskeController',
        'mail_admin_group'     => 'MailBundle\Controller\Admin\GroupController',
        'mail_admin_list'      => 'MailBundle\Controller\Admin\MailingListController',
        'mail_admin_message'   => 'MailBundle\Controller\Admin\MessageController',
        'mail_admin_prof'      => 'MailBundle\Controller\Admin\ProfController',
        'mail_admin_study'     => 'MailBundle\Controller\Admin\StudyController',
        'mail_admin_volunteer' => 'MailBundle\Controller\Admin\VolunteerController',
    ),
);
