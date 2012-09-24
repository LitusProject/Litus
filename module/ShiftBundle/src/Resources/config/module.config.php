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
            'shift_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/install/shift',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'shift_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_shift' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/shift[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_shift',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_unit' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/unit[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_unit',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'shift' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '[/:language]/shift[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'name'     => '[a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'shift',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'shift_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'ShiftBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'shiftbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/shift.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/shift.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'shift_install' => 'ShiftBundle\Controller\Admin\InstallController',
            'admin_shift'   => 'ShiftBundle\Controller\Admin\ShiftController',
            'admin_unit'    => 'ShiftBundle\Controller\Admin\UnitController',

            'shift'         => 'ShiftBundle\Controller\ShiftController',
        ),
    ),
);