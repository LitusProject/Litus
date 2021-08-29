<?php

return array(
    'routes' => array(
        'notification_admin_notification' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/site/notification[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'notification_admin_notification',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'notification_admin_notification' => 'NotificationBundle\Controller\Admin\NotificationController',
    ),
);
