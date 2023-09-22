<?php

return array(
    'routes' => array(
        'mail_admin_alias' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/alias[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_alias',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_bakske' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/bakske[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_bakske',
                    'action'     => 'send',
                ),
            ),
        ),
        'mail_admin_preference' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/preference[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_preference',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_list' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/list[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_list',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_message' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/message[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-z0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_message',
                    'action'     => 'manage',
                ),
            ),
        ),
        'mail_admin_prof' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/prof[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_prof',
                    'action'     => 'cudi',
                ),
            ),
        ),
        'mail_admin_promotion' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/promotion[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_promotion',
                    'action'     => 'send',
                ),
            ),
        ),
        'mail_admin_study' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/study[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_study',
                    'action'     => 'send',
                ),
            ),
        ),
        'mail_admin_volunteer' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/mail/volunteer[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'mail_admin_volunteer',
                    'action'     => 'send',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'mail_admin_alias'          => 'MailBundle\Controller\Admin\AliasController',
        'mail_admin_bakske'         => 'MailBundle\Controller\Admin\BakskeController',
        'mail_admin_preference'     => 'MailBundle\Controller\Admin\PreferenceController',
        'mail_admin_group'          => 'MailBundle\Controller\Admin\GroupController',
        'mail_admin_list'           => 'MailBundle\Controller\Admin\MailingListController',
        'mail_admin_message'        => 'MailBundle\Controller\Admin\MessageController',
        'mail_admin_prof'           => 'MailBundle\Controller\Admin\ProfController',
        'mail_admin_promotion'      => 'MailBundle\Controller\Admin\PromotionController',
        'mail_admin_study'          => 'MailBundle\Controller\Admin\StudyController',
        'mail_admin_volunteer'      => 'MailBundle\Controller\Admin\VolunteerController',
    ),
);
