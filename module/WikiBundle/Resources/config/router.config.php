<?php

return array(
    'routes' => array(
        'wiki_auth' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/wiki/auth[/:action[/identification/:identification[/hash/:hash]][/redirect/:redirect]][/]',
                'constraints' => array(
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'wiki_auth',
                    'action'     => 'login',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'wiki_auth' => 'WikiBundle\Controller\AuthController',
    ),
);
