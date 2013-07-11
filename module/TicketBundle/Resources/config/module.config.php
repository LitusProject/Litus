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
            'ticket_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/ticket[/]',
                    'defaults' => array(
                        'controller' => 'ticket_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'ticket_admin_event' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/ticket/event[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'ticket_admin_event',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'ticket_admin_ticket' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/ticket/ticket[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'ticket_admin_ticket',
                        'action'     => 'manage',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ticket_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'TicketBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'ticketbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ticket_install'               => 'TicketBundle\Controller\Admin\InstallController',
            'ticket_admin_event'           => 'TicketBundle\Controller\Admin\EventController',
            'ticket_admin_ticket'          => 'TicketBundle\Controller\Admin\TicketController',
        ),
    ),
);
