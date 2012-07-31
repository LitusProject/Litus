<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
$asseticConfig = include __DIR__ . '/../../../../../config/assetic.config.php';

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'page_install' => 'PageBundle\Controller\Admin\InstallController',
                
                'admin_page'   => 'PageBundle\Controller\Admin\PageController',

                'page'          => 'PageBundle\Controller\PageController',
            ),
            
            'doctrine_config' => array(
                'parameters' => array(
                    'entityPaths' => array(
                        'pagebundle' => __DIR__ . '/../../Entity',
                    ),
                ),
            ),
        ),
    ),
    'routes' => array(
        'page' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '[/:language]/page[/:id]',
                'constraints' => array(
                    'id'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '[a-zA-Z][a-zA-Z_-]*',
                ),
                'defaults' => array(
                    'controller' => 'page',
                    'action'     => 'view',
                ),
            ),
        ),
        'page_install' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/install/page',
                'constraints' => array(
                ),
                'defaults' => array(
                    'controller' => 'page_install',
                    'action'     => 'index',
                ),
            ),
        ),
        'admin_page' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/content/page[/:action[/:id]]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'admin_page',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),
);
