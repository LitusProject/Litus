<?php

return array(
    'routes' => array(
        'sport_admin_run' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/run[/:action[/:id]][/page/:page][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'sport_admin_run',
                    'action'     => 'laps',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'sport_admin_run' => 'SportBundle\Controller\Admin\RunController',
    ),
);
