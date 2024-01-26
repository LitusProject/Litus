<?php

return array(
    'routes' => array(
        'ticket_admin_event' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/ticket/event[/:action[/:id]][/page/:page][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_admin_event',
                    'action'     => 'manage',
                ),
            ),
        ),
        'ticket_admin_ticket' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/ticket/ticket[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_admin_ticket',
                    'action'     => 'manage',
                ),
            ),
        ),
        'ticket_admin_consumptions' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/ticket/consumptions[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_admin_consumptions',
                    'action'     => 'manage',
                ),
            ),
        ),
        'ticket_sale_consume' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/consume[/:action][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_sale_consume',
                    'action'     => 'consume',
                ),
            ),
        ),
        'ticket_sale_index' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/ticket/sale[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_sale_index',
                    'action'     => 'sale',
                ),
            ),
        ),
        'ticket_sale_ticket' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/ticket/sale/ticket[/:action[/:id][/:ticket][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'ticket' => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_sale_ticket',
                    'action'     => 'overview',
                ),
            ),
        ),
        'ticket_sale_person_typeahead' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/ticket/person/typeahead[/:string][/]',
                'constraints' => array(
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket_sale_person',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'ticket' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/ticket[/:action[/:id[/code/:code][/qr/:qr]]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[a-zA-Z0-9_-]*',
                    'code'     => '[0-9]*',
                    'language' => '(en|nl)',
                    'qr'       => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'ticket',
                    'action'     => 'event',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'ticket_admin_event'        => 'TicketBundle\Controller\Admin\EventController',
        'ticket_admin_ticket'       => 'TicketBundle\Controller\Admin\TicketController',
        'ticket_admin_consumptions' => 'TicketBundle\Controller\Admin\ConsumptionsController',

        'ticket_sale_index'         => 'TicketBundle\Controller\Sale\IndexController',
        'ticket_sale_ticket'        => 'TicketBundle\Controller\Sale\TicketController',
        'ticket_sale_person'        => 'TicketBundle\Controller\Sale\PersonController',

        'ticket_sale_consume'       => 'TicketBundle\Controller\Sale\ConsumeController',
        'ticket'                    => 'TicketBundle\Controller\TicketController',
    ),
);
