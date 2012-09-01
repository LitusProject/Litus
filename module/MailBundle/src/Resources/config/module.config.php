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
    'router' => array(
        'routes' => array(
            'mail_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/mail',
                    'defaults' => array(
                        'controller' => 'mail_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_mail' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/mail/groups[/:action[/:type/:group]]',
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
            'admin_mail_prof' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/mail/prof[/:action]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_mail_prof',
                        'action'     => 'cudi',
                    ),
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
            'mail_install'                 => 'MailBundle\Controller\Admin\InstallController',
            'admin_mail'                   => 'MailBundle\Controller\Admin\MailController',
            'admin_mail_prof'              => 'MailBundle\Controller\Admin\ProfController',
        ),
    ),
);
