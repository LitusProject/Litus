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
            'publication_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/publication',
                    'defaults' => array(
                        'controller' => 'publication_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_publication' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/publication[/:action[/:id][/page/:page][/:field/:string]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_publication',
                        'action'     => 'manage',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'publication_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'PublicationBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'publicationbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'publication_install'      => 'PublicationBundle\Controller\Admin\InstallController',

            'admin_publication'        => 'PublicationBundle\Controller\Admin\PublicationController',
        ),
    ),
);
