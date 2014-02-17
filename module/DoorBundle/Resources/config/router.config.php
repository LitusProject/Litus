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
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'routes' => array(
        'door_install' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/install/door[/]',
                'constraints' => array(
                ),
                'defaults' => array(
                    'controller' => 'door_install',
                    'action'     => 'index',
                ),
            ),
        ),
        'door_admin_rule' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/door/rule[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[a-z0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'door_admin_rule',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'door_install'    => 'DoorBundle\Controller\Admin\InstallController',
        'door_admin_rule' => 'DoorBundle\Controller\Admin\RuleController',
    ),
);
