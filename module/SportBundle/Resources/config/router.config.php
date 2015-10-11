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
        'sport_admin_run' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/run[/:action[/:id]][/page/:page][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'sport_admin_run',
                    'action'     => 'queue',
                ),
            ),
        ),
        'sport_run_index' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/run[/:action][/]',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'sport_run_index',
                    'action'     => 'index',
                ),
            ),
        ),
        'sport_run_group' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/run/group[/:action[/:university_identification]][/]',
                'constraints' => array(
                    'language'                  => '[a-z]{2}',
                    'action'                    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'university_identification' => '[a-z]{1}[0-9]{7}',
                ),
                'defaults' => array(
                    'controller' => 'sport_run_group',
                    'action'     => 'add',
                ),
            ),
        ),
        'sport_run_queue' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/run/queue[/:action[/:university_identification]][/]',
                'constraints' => array(
                    'language'                  => '[a-z]{2}',
                    'action'                    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'university_identification' => '[a-z0-9]{1}[0-9]{7}',
                ),
                'defaults' => array(
                    'controller' => 'sport_run_queue',
                    'action'     => 'index',
                ),
            ),
        ),
        'sport_run_screen' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/run/screen[/:action][/]',
                'constraints' => array(
                    'language' => '[a-z]{2}',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'sport_run_screen',
                    'action'     => 'index',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'sport_admin_run'  => 'SportBundle\Controller\Admin\RunController',

        'sport_run_index'  => 'SportBundle\Controller\Run\IndexController',
        'sport_run_group'  => 'SportBundle\Controller\Run\GroupController',
        'sport_run_queue'  => 'SportBundle\Controller\Run\QueueController',
        'sport_run_screen' => 'SportBundle\Controller\Run\ScreenController',
    ),
);
