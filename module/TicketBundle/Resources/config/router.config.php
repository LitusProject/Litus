<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'routes' => array(
        'ticket_admin_event' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/ticket/event[/:action[/:id]][/page/:page][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
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
                'route'       => '/admin/ticket/ticket[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'ticket_admin_ticket',
                    'action'     => 'manage',
                ),
            ),
        ),
        'ticket_sale_index' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'       => '/ticket/sale[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'ticket_sale_index',
                    'action'     => 'sale',
                ),
            ),
        ),
        'ticket_sale_ticket' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'       => '/ticket/sale/ticket[/:action[/:id[/:ticket]][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'ticket' => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'ticket_sale_ticket',
                    'action'     => 'overview',
                ),
            ),
        ),
        'ticket_sale_person_typeahead' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'       => '/ticket/person/typeahead[/:string][/]',
                'constraints' => array(
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'ticket_sale_person',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'ticket' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/ticket[/:action[/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9]*',
                    'language' => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'ticket',
                    'action'     => 'event',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'ticket_admin_event'  => 'TicketBundle\Controller\Admin\EventController',
        'ticket_admin_ticket' => 'TicketBundle\Controller\Admin\TicketController',

        'ticket_sale_index'  => 'TicketBundle\Controller\Sale\IndexController',
        'ticket_sale_ticket' => 'TicketBundle\Controller\Sale\TicketController',
        'ticket_sale_person' => 'TicketBundle\Controller\Sale\PersonController',

        'ticket' => 'TicketBundle\Controller\TicketController',
    ),
);
