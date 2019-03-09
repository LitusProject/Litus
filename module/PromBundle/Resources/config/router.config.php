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
        'prom_admin_bus' => array(
            'type'    => 'Zend\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/prom/bus[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_admin_bus',
                    'action'     => 'manage',
                ),
            ),
        ),
        'prom_admin_code' => array(
            'type'    => 'Zend\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/prom/code[/:action[/:id][/:field/:string][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_admin_code',
                    'action'     => 'manage',
                ),
            ),
        ),
        'prom_admin_passenger' => array(
            'type'    => 'Zend\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/prom/passenger[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_admin_passenger',
                    'action'     => 'manage',
                ),
            ),
        ),
        'prom_registration_index' => array(
            'type'    => 'Zend\Router\Http\Segment',
            'options' => array(
                'route'       => '/prom/registration[/:action[/:code]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'code'   => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'prom_registration_index',
                    'action'     => 'registration',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'prom_admin_bus'       => 'PromBundle\Controller\Admin\BusController',
        'prom_admin_code'      => 'PromBundle\Controller\Admin\CodeController',
        'prom_admin_passenger' => 'PromBundle\Controller\Admin\PassengerController',

        'prom_registration_index' => 'PromBundle\Controller\Registration\IndexController',
    ),
);
