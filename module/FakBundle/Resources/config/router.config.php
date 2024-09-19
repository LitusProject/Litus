<?php

return array(
    'routes' => array(
        'fak_admin_scanner' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/fak[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'fak_admin_scanner',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'fak_admin_scanner' => 'FakBundle\Controller\Admin\ScannerController',
    ),
);
