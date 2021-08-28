<?php

return array(
    'routes' => array(
        'door_admin_rule' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/door/rule[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-z0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'door_admin_rule',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'door_admin_rule' => 'DoorBundle\Controller\Admin\RuleController',
    ),
);
