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
        'form_admin_form' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/form[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'form_admin_form',
                    'action'     => 'manage',
                ),
            ),
        ),
        'form_admin_group' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/form/group[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'form_admin_group',
                    'action'     => 'manage',
                ),
            ),
        ),
        'form_admin_form_field' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/form/field[/:action[/:id][/page/:page][/repeat/:repeat]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'repeat'  => '[1]',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'form_admin_form_field',
                    'action'     => 'manage',
                ),
            ),
        ),
        'form_admin_form_viewer' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/form/viewer[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'form_admin_form_viewer',
                    'action'     => 'manage',
                ),
            ),
        ),
        'form_admin_group_viewer' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/form/group/viewer[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'form_admin_group_viewer',
                    'action'     => 'manage',
                ),
            ),
        ),
        'form_view' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/form[/:action[/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'form_view',
                    'action'     => 'view',
                ),
            ),
        ),
        'form_group' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/form/group[/:action[/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'form_group',
                    'action'     => 'view',
                ),
            ),
        ),
        'form_manage' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/form/manage[/:action[/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'form_manage',
                    'action'     => 'index',
                ),
            ),
        ),
        'form_manage_group' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/form/manage/group[/:action[/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'form_manage_group',
                    'action'     => 'index',
                ),
            ),
        ),
        'form_manage_mail' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/form/manage/mail[/:action[/:id]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'form_manage_mail',
                    'action'     => 'send',
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

    'controllers' => array(
        'form_admin_form'         => 'FormBundle\Controller\Admin\FormController',
        'form_admin_group'        => 'FormBundle\Controller\Admin\GroupController',
        'form_admin_form_field'   => 'FormBundle\Controller\Admin\FieldController',
        'form_admin_form_viewer'  => 'FormBundle\Controller\Admin\ViewerController',
        'form_admin_group_viewer' => 'FormBundle\Controller\Admin\GroupViewerController',

        'form_view'               => 'FormBundle\Controller\FormController',
        'form_group'              => 'FormBundle\Controller\GroupController',
        'form_manage'             => 'FormBundle\Controller\Manage\FormController',
        'form_manage_group'       => 'FormBundle\Controller\Manage\GroupController',
        'form_manage_mail'        => 'FormBundle\Controller\Manage\MailController',
        'form_manage_auth'        => 'FormBundle\Controller\Manage\AuthController',
    ),
);
