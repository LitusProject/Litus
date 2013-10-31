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
                    'route' => '/admin/ticket/event[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
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
                    'route' => '/admin/ticket/ticket[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
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
                    'route' => '/ticket/sale[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
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
                    'route' => '/ticket/sale/ticket[/:action[/:id[/:ticket]][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'ticket'  => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'ticket_sale_ticket',
                        'action'     => 'sale',
                    ),
                ),
            ),
            'ticket_sale_person_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/ticket/person/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'ticket_sale_person',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'ticket' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/ticket[/:action[/:id]][/]',
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
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ticket_layout' => __DIR__ . '/../layouts',
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

            'ticket_sale_index'            => 'TicketBundle\Controller\Sale\IndexController',
            'ticket_sale_ticket'           => 'TicketBundle\Controller\Sale\TicketController',
            'ticket_sale_person'           => 'TicketBundle\Controller\Sale\PersonController',

            'ticket'                       => 'TicketBundle\Controller\TicketController',
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/ticket.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/ticket.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'ticketbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'ticket_css' => array(
                        'assets' => array(
                            'ticket/less/base.less',
                        ),
                        'filters' => array(
                            'ticket_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'ticket_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);