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
            'notification_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/install/notification',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'notification_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_notification' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/site/notification[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_notification',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'notification' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '[/:language]/notification[/:action[/:name][/page/:page]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z0-9_-]*',
                        'name'     => '[a-zA-Z0-9_-]*',
                        'page'     => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'notification',
                        'action'     => 'overview',
                    ),
                ),
            ),
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
    'view_manager' => array(
        'template_path_stack' => array(
            'notification_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'NotificationBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'notificationbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'notification_install' => 'NotificationBundle\Controller\Admin\InstallController',
            'admin_notification'   => 'NotificationBundle\Controller\Admin\NotificationController',

            'notification'  => 'NotificationBundle\Controller\NotificationController',
        ),
    ),
);