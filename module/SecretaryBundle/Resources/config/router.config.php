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
        'secretary_admin_registration' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/secretary/registration[/:action[/:id][/organization/:organization]][/:academicyear][/:field/:string][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'organization' => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'secretary_admin_registration',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_admin_export' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/secretary/export[/:action[/:id][/organization/:organization]][/:academicyear][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'organization' => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                ),
                'defaults' => array(
                    'controller' => 'secretary_admin_export',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_admin_photos' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/secretary/photos[/:action][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'secretary_admin_photos',
                    'action'     => 'photos',
                ),
            ),
        ),
        'secretary_admin_promotion' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/secretary/promotion[/:action[/:id][/page/:page]][/:academicyear][/:field/:string][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'page'         => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults' => array(
                    'controller' => 'secretary_admin_promotion',
                    'action'     => 'manage',
                ),
            ),
        ),
        'secretary_registration' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '[/:language]/secretary/registration[/:action][/identification/:identification[/hash/:hash]][/]',
                'constraints' => array(
                    'language'       => '[a-z]{2}',
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'secretary_registration',
                    'action'     => 'add',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'secretary_admin_registration'   => 'SecretaryBundle\Controller\Admin\RegistrationController',
        'secretary_admin_export'         => 'SecretaryBundle\Controller\Admin\ExportController',
        'secretary_admin_promotion'      => 'SecretaryBundle\Controller\Admin\PromotionController',
        'secretary_admin_photos'         => 'SecretaryBundle\Controller\Admin\PhotosController',
        'secretary_registration'         => 'SecretaryBundle\Controller\RegistrationController',
    ),
);
